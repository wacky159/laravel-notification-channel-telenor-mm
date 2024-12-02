<?php

declare(strict_types=1);

namespace NotificationChannels\TelenorMM\Support;

use Illuminate\Support\Facades\Log;

trait HasDebugLog
{
    /**
     * Log a debug message
     *
     * @param string $message The message to log
     * @param array $context Additional context information
     */
    protected function logDebug(string $message, array $context = []): void
    {
        if (!config('telenor-mm.log.enabled', false)) {
            return;
        }

        $className = class_basename($this);

        Log::channel(
            config('telenor-mm.log.channel', 'stack')
        )->debug(
            sprintf('[TelenorMM:%s] %s', $className, $message),
            $context
        );
    }
}
