<?php

namespace Wacky159\TelenorMM\Exceptions;

class CouldNotSendNotification extends \Exception
{
    /**
     * When the content parameter is invalid
     * Error code: 400.032.001
     */
    public static function invalidContent($response)
    {
        return new static("Invalid content parameter: {$response['message']} (Error code: {$response['code']})");
    }

    /**
     * When the sender name is missing or invalid
     * Error code: 400.032.002
     */
    public static function invalidSenderName($response)
    {
        return new static("Invalid sender name: {$response['message']} (Error code: {$response['code']})");
    }

    /**
     * When the phone number is invalid
     * Error code: 400.032.005
     */
    public static function invalidPhoneNumber($response)
    {
        return new static("Invalid phone number: {$response['message']} (Error code: {$response['code']})");
    }

    /**
     * When the access token is invalid
     * Error code: 401.000.2001
     */
    public static function invalidAccessToken($response)
    {
        return new static("Invalid access token: {$response['message']} (Error code: {$response['code']})");
    }

    /**
     * Handle other unexpected error responses
     */
    public static function serviceRespondedWithAnError($response)
    {
        return new static("Telenor MM API returned an error: {$response['message']} (Error code: {$response['code']})");
    }

    /**
     * When the rate limit is exceeded
     */
    public static function rateLimitExceeded($response)
    {
        return new static("Rate limit exceeded: {$response['message']} (Error code: {$response['code']})");
    }

    /**
     * When a network error occurs
     */
    public static function networkError($exception)
    {
        return new static("Network error occurred: {$exception->getMessage()}");
    }

    /**
     * When the configuration is missing
     */
    public static function missingConfiguration($message)
    {
        return new static("Missing configuration: {$message}");
    }
}
