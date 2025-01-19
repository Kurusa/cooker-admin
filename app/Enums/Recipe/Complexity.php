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

    public static function mapParsedValue(string $value): self
    {
        return match ($value) {
            'складно', 'hard' => self::HARD,
            'елементарно', 'легко', 'easy' => self::EASY,
            default => self::MEDIUM,
        };
    }
}
