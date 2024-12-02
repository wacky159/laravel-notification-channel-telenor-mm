<?php

namespace Wacky159\TelenorMM\Exceptions;

class CouldNotSendNotification extends \Exception
{
    /**
     * When the SMS credentials are invalid
     * Error code: 400.032.201
     */
    public static function invalidCredentials($response)
    {
        return new static("Invalid SMS credentials: {$response['message']} (Error code: {$response['code']})");
    }

    /**
     * When the sender name is invalid
     * Error code: 400.032.203
     */
    public static function invalidSenderName($response)
    {
        return new static("Invalid sender name: {$response['message']} (Error code: {$response['code']})");
    }

    /**
     * When SMSC internal disconnect occurs
     * Error code: 400.032.204
     */
    public static function smscInternalDisconnect($response)
    {
        return new static("SMSC Internal disconnect: {$response['message']} (Error code: {$response['code']})");
    }

    /**
     * When content length exceeds limit
     * Error codes: 400.032.207, 400.032.208
     */
    public static function contentLengthExceeded($response)
    {
        return new static("Content length exceeded: {$response['message']} (Error code: {$response['code']})");
    }

    /**
     * When chunks exceeded allowed limit
     * Error code: 400.032.222
     */
    public static function chunksExceeded($response)
    {
        return new static("Exceeded allowed chunks: {$response['message']} (Error code: {$response['code']})");
    }

    /**
     * When access is denied
     * Error code: 403.032.001
     */
    public static function accessDenied($response)
    {
        return new static("Access denied: {$response['message']} (Error code: {$response['code']})");
    }

    /**
     * When system error occurs
     * Error code: 500.032.001
     */
    public static function systemError($response)
    {
        return new static("System error: {$response['message']} (Error code: {$response['code']})");
    }

    /**
     * When service is unavailable
     * Error code: 503.032.001
     */
    public static function serviceUnavailable($response)
    {
        return new static("Service unavailable: {$response['message']} (Error code: {$response['code']})");
    }

    /**
     * When rate limit is exceeded (spike arrest)
     * Error code: 429.032.001
     */
    public static function spikeArrestViolation($response)
    {
        return new static("Spike arrest violation: {$response['message']} (Error code: {$response['code']})");
    }

    /**
     * When rate limit quota is exceeded
     * Error code: 429.032.002
     */
    public static function quotaLimitExceeded($response)
    {
        return new static("Rate limit quota exceeded: {$response['message']} (Error code: {$response['code']})");
    }

    /**
     * Handle other unexpected error responses
     */
    public static function serviceRespondedWithAnError($response)
    {
        return new static("Telenor MM API returned an error: {$response['message']} (Error code: {$response['code']})");
    }

    /**
     * When a network error occurs
     */
    public static function networkError($exception)
    {
        return new static("Network error occurred: {$exception->getMessage()}");
    }
}
