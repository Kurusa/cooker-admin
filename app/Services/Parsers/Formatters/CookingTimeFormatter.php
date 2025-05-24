<?php

namespace App\Services\Parsers\Formatters;

class CookingTimeFormatter
{
    /**
     * Форматує час приготування у хвилинах.
     *
     * Цей метод приймає рядок, що містить час приготування, який може включати слова, як-от "хвилин", "хвилина", "години", "година".
     * Він повертає кількість хвилин у вигляді цілого числа, враховуючи години та хвилини.
     *
     * Приклади:
     * - "30 хвилин" => 30
     * - "1 година" => 60
     * - "2 години" => 120
     * - "1 година 15 хв" => 75
     * - "2 години 30 хв" => 150
     *
     * @param string $time Час у текстовому форматі, який потрібно відформатувати.
     *
     * @return int Повертає час у хвилинах.
     */
    public static function formatCookingTime(string $time): int
    {
        $time = mb_strtolower($time);

        $minutes = 0;
        $hours = 0;

        if (preg_match('/(\d+)\s*(година|години|год|hours)/iu', $time, $matches)) {
            $hours = (int)$matches[1];
        }

        if (preg_match('/(\d+)\s*(хвилина|хвилини|хв|min|minutes)/iu', $time, $matches)) {
            $minutes = (int)$matches[1];
        }

        if (preg_match('/^\d+$/', $time)) {
            $minutes = (int)$time;
        }

        return $hours * 60 + $minutes;
    }
}
