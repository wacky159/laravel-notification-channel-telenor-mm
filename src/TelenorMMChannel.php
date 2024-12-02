<?php

namespace Wacky159\TelenorMM;

use Wacky159\TelenorMM\Exceptions\CouldNotSendNotification;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Wacky159\TelenorMM\Contracts\AuthorizationCodeProvider;
use Wacky159\TelenorMM\Support\HasDebugLog;
use Illuminate\Http\Client\Response;

class TelenorMMChannel
{
    use HasDebugLog;

    protected $authCodeProvider;
    protected $apiUrl;

    public function __construct(
        AuthorizationCodeProvider $authCodeProvider
    ) {
        $this->authCodeProvider = $authCodeProvider;
        $this->apiUrl = rtrim(config('telenor-mm.api_url'), '/');
    }

    /**
     * Send the specified notification
     *
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     *
     * @throws \NotificationChannels\TelenorMM\Exceptions\CouldNotSendNotification
     */
    public function send(mixed $notifiable, Notification $notification): array
    {
        if (!method_exists($notification, 'toTelenorMM')) {
            $this->logDebug('Notification does not have toTelenorMM method');
            return [];
        }

        $message = $notification->toTelenorMM($notifiable);
        $tries = 0;
        $maxTries = config('telenor-mm.retry.max_attempts', 3);

        $this->logDebug('Preparing to send message', [
            'message' => $message->toArray(),
            'max_tries' => $maxTries
        ]);

        do {
            try {
                $this->logDebug('Attempting to send message', ['attempt' => $tries + 1]);
                $response = $this->sendMessage($message);
                $this->logDebug('Message sent successfully', ['response' => $response]);
                return $response;
            } catch (CouldNotSendNotification $e) {
                $tries++;
                $this->logDebug('Failed to send message', [
                    'attempt' => $tries,
                    'error' => $e->getMessage()
                ]);

                if ($tries >= $maxTries || $maxTries === 1) {
                    $this->logDebug('Max retry attempts reached or no retry configured', [
                        'max_tries' => $maxTries,
                        'current_try' => $tries
                    ]);
                    throw $e;
                }

                if (strpos($e->getMessage(), '401') !== false) {
                    $this->logDebug('Authentication error, clearing token cache');
                    Cache::forget('telenor_mm_access_token');
                }

                $delay = config('telenor-mm.retry.delay', 1);
                $this->logDebug("Waiting {$delay} seconds before retry");
                sleep($delay);
            }
        } while ($tries < $maxTries);

        return [];
    }

    protected function sendMessage(TelenorMMMessage $message): array
    {
        $this->logDebug('Getting access token');
        $accessToken = $this->getAccessToken();

        $this->logDebug('Sending request to Telenor API', [
            'url' => "{$this->apiUrl}/v3/mm/en/communicationMessage/send",
            'payload' => $message->toArray()
        ]);

        $response = Http::withHeaders([
            'Authorization' => "Bearer $accessToken",
            'Content-Type' => 'application/json',
        ])->post("{$this->apiUrl}/v3/mm/en/communicationMessage/send", $message->toArray());

        if ($response->failed()) {
            $this->logDebug('Request failed', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);
            $this->handleError($response);
        }

        return $response->json();
    }

    protected function handleError(Response $response): void
    {
        $responseBody = $response->json();

        throw match($responseBody['code'] ?? '') {
            '400.032.201' => CouldNotSendNotification::invalidCredentials($responseBody),
            '400.032.203' => CouldNotSendNotification::invalidSenderName($responseBody),
            '400.032.204' => CouldNotSendNotification::smscInternalDisconnect($responseBody),
            '400.032.207', '400.032.208' => CouldNotSendNotification::contentLengthExceeded($responseBody),
            '400.032.222' => CouldNotSendNotification::chunksExceeded($responseBody),
            '403.032.001' => CouldNotSendNotification::accessDenied($responseBody),
            '500.032.001' => CouldNotSendNotification::systemError($responseBody),
            '503.032.001' => CouldNotSendNotification::serviceUnavailable($responseBody),
            '429.032.001' => CouldNotSendNotification::spikeArrestViolation($responseBody),
            '429.032.002' => CouldNotSendNotification::quotaLimitExceeded($responseBody),
            default => CouldNotSendNotification::serviceRespondedWithAnError($responseBody)
        };
    }

    /**
     * Get the access token
     * If there is no valid token in the cache, it will try to get a new one
     */
    protected function getAccessToken(): ?string
    {
        $this->logDebug('Attempting to get access token from cache');
        $accessToken = Cache::get('telenor_mm_access_token');

        if ($accessToken) {
            $this->logDebug('Access token found in cache');
            return $accessToken;
        }

        $this->logDebug('Access token not found in cache, requesting new token');
        $accessToken = $this->requestAccessToken();

        if (!$accessToken) {
            $this->logDebug('First attempt to get access token failed, refreshing authorization code');
            $this->refreshAuthorizationCode();

            $this->logDebug('Attempting to get access token again after refresh');
            $accessToken = $this->requestAccessToken();

            if (!$accessToken) {
                $this->logDebug('Failed to get access token after refresh');
                throw new \Exception('Failed to fetch access token after revalidation.');
            }
        }

        return $accessToken;
    }

    /**
     * Request a new access token
     * Use the authorization code to get a new access token from the API
     */
    protected function requestAccessToken(): ?string
    {
        // Get the authorization code from the cache
        $authorizationCode = Cache::get('telenor_mm_authorization_code');

        if (!$authorizationCode) {
            $this->logDebug('Authorization code not found. Cannot fetch access token.');
            return null;
        }

        // Send a request to get the access token
        $response = Http::asForm()->post("{$this->apiUrl}/oauth/v1/token", [
            'grant_type' => 'authorization_code',
            'client_id' => config('telenor-mm.client_id'),
            'client_secret' => config('telenor-mm.client_secret'),
            'code' => $authorizationCode,
        ]);

        if ($response->status() !== 200) {
            $this->logDebug('Failed to fetch access token', ['response' => $response->body()]);
            return null;
        }

        // 成功使用後立即移除 authorization code
        Cache::forget('telenor_mm_authorization_code');
        $this->logDebug('Authorization code removed from cache after successful use');

        $data = $response->json();

        if (empty($data['accessToken'])) {
            $this->logDebug('Invalid access token response', ['response' => $response->body()]);
            return null;
        }

        // Store the access token and refresh token in the cache
        Cache::put('telenor_mm_access_token', $data['accessToken'], $data['expiresIn']);
        Cache::put('telenor_mm_refresh_token', $data['refresh_token'], 86400);

        return $data['accessToken'];
    }

    /**
     * Refresh the authorization code
     * Use it when the current authorization code expires
     */
    protected function refreshAuthorizationCode(): void
    {
        $response = Http::get("{$this->apiUrl}/oauth/v1/userAuthorize", [
            'client_id' => config('telenor-mm.client_id'),
            'response_type' => 'code',
        ]);

        if ($response->status() !== 200) {
            $this->logDebug('Failed to refresh authorization code', ['response' => $response->body()]);
            throw new \Exception('Failed to refresh authorization code.');
        }
    }

    protected function getAuthorizationCode(): ?string
    {
        return $this->authCodeProvider->getAuthorizationCode();
    }
}
