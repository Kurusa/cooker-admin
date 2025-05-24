<?php

namespace App\Enums\Recipe;

enum Complexity: string
{
    case EASY = 'easy';
    case MEDIUM = 'medium';
    case HARD = 'hard';

    public function getEmoji(): string
    {
        return match ($this) {
            self::EASY => '🟢',
            self::MEDIUM => '🟠',
            self::HARD => '🔴',
        };
    }

    public function getBadgeColor(): string
    {
        return match ($this) {
            self::EASY => '#22c55e',
            self::MEDIUM => '#f59e0b',
            self::HARD => '#ef4444',
        };
    }

    public static function mapParsedValue(string $value): self
    {
        return match ($value) {
            'складно', 'hard', 'висока, потребує практики та навиків' => self::HARD,
            'елементарно', 'легко', 'easy' => self::EASY,
            default => self::MEDIUM,
        };
    }
}
