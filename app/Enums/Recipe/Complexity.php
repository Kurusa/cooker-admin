<?php

namespace App\Enums\Recipe;

enum Complexity: string
{
    case EASY = 'easy';
    case MEDIUM = 'medium';
    case HARD = 'hard';

    public function getDescription(): string
    {
        return match ($this) {
            self::EASY => self::getEmoji() . __('texts.easy'),
            self::MEDIUM => self::getEmoji() . __('texts.medium'),
            self::HARD => self::getEmoji() . __('texts.hard'),
        };
    }

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
            'складно' => self::HARD,
            'елементарно', 'легко' => self::EASY,
            default => self::MEDIUM,
        };
    }
}
