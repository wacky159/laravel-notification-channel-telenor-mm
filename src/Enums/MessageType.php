<?php

declare(strict_types=1);

namespace NotificationChannels\TelenorMM\Enums;

enum MessageType: string
{
    case TEXT = 'TEXT';
    case BINARY = 'BINARY';
    case MULTILINGUAL = 'MULTILINGUAL';
    case FLASH = 'FLASH';

    /**
     * Get all available message types
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Check if the given type is a valid message type
     */
    public static function isValid(string $type): bool
    {
        return in_array(strtoupper($type), self::values(), true);
    }
}
