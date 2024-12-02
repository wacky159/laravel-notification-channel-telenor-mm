<?php

declare(strict_types=1);

namespace Wacky159\TelenorMM\Enums;

enum SenderType: int
{
    case UNKNOWN = 0;
    case INTERNATIONAL = 1;
    case NATIONAL = 2;
    case NETWORK_SPECIFIC = 3;
    case SUBSCRIBER_NUMBER = 4;
    case ALPHANUMERIC = 5;
    case ABBREVIATED = 6;

    /**
     * Get all available sender types
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get the description of the sender type
     */
    public function description(): string
    {
        return match($this) {
            self::UNKNOWN => 'Unknown type',
            self::INTERNATIONAL => 'International number',
            self::NATIONAL => 'National number',
            self::NETWORK_SPECIFIC => 'Network-specific number',
            self::SUBSCRIBER_NUMBER => 'Subscriber number',
            self::ALPHANUMERIC => 'Alphanumeric',
            self::ABBREVIATED => 'Abbreviated'
        };
    }
}
