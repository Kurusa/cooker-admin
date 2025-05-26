<?php

namespace App\Enums\Source;

enum SourceStatus: string
{
    case EMPTY = 'empty';
    case COLLECTED = 'collected';
    case PARTIALLY_PARSED = 'partially_parsed';
    case PARSED = 'parsed';
    case EXCLUDED_ONLY = 'excluded_only';

    public function getBadgeColor(): string
    {
        return match ($this) {
            self::EMPTY => '#9ca3af',
            self::COLLECTED => '#fbbf24',
            self::PARTIALLY_PARSED => '#3b82f6',
            self::PARSED => '#10b981',
            self::EXCLUDED_ONLY => '#f43f5e',
        };
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::EMPTY => 'Empty',
            self::COLLECTED => 'Collected',
            self::PARTIALLY_PARSED => 'Partially parsed',
            self::PARSED => 'Parsed',
            self::EXCLUDED_ONLY => 'Excluded only',
        };
    }
}
