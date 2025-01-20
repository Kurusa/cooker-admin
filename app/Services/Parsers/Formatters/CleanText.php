<?php

namespace App\Services\Parsers\Formatters;

class CleanText
{
    public static function cleanText(string $text): string
    {
        $text = trim($text);
        $text = ltrim($text);

        $text = mb_strtolower($text);

        $text = rtrim($text, ',');
        $text = rtrim($text, '.');

        $text = ltrim($text, '-');
        $text = ltrim($text, '–');
        $text = rtrim($text, '-');
        $text = rtrim($text, '–');

        $text = ltrim($text, ':');
        $text = rtrim($text, ':');

        $text = preg_replace('/\x{00A0}/u', '', $text);

        $text = trim($text);
        $text = ltrim($text);

        $text = str_replace(['“', '”', '„', '"', '‟', "’", "«", '»'], "'", $text);
        $text = str_replace(' %', '%', $text);
        $text = str_replace('( ', ' (', $text);
        $text = str_replace(' )', ')', $text);

        return $text;
    }

    public static function cleanQuantity(string $quantity): string
    {
        $text = str_replace(',', '.', $quantity);

        return $text;
    }
}
