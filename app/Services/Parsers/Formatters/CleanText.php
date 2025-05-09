<?php

namespace App\Services\Parsers\Formatters;

class CleanText
{
    public static function cleanText(string $text): string
    {
        $text = trim($text);
        $text = ltrim($text);
        $text = str_replace([
            "\r", "\n",
        ], '', $text);
        $text = mb_strtolower($text);

        $text = rtrim($text, ',');
        $text = rtrim($text, '.');
        $text = rtrim($text, '-');

        $text = preg_replace('/\x{00A0}/u', '', $text);

        $text = str_replace(['“', '”', '„', '"', '‟', "’", "«", '»'], "'", $text);
        $text = str_replace(' %', '%', $text);
        $text = str_replace('( ', ' (', $text);
        $text = str_replace(' )', ')', $text);

        $text = preg_replace('/\s+/', ' ', $text);

        $text = rtrim($text, ':');
        $text = rtrim($text, ';');

        $text = preg_replace('/^\d+\.\s+/', ' ', $text);

        $text = trim($text);
        $text = ltrim($text);

        return $text;
    }
}
