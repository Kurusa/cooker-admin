<?php
$ingredient = "сухі дріжджі - ¼ ч. ложки";

function cleanText(string $text): ?string
{
    $text = trim($text);
    $text = mb_strtolower($text);
    $text = rtrim($text, ',.');
    $text = ltrim($text, '-');
    return $text;
}

$pattern = '/^(.*?)\s*[-–—:]\s*((?:\d+[.,]?\d*|\d+\/\d+)(?:-\d+[.,]?\d*|\d+\/\d+)?)(?:\s+([^\d]+))?$/u';

if (preg_match($pattern, $ingredient, $matches)) {
    $title = cleanText($matches[1]);
    $quantity = isset($matches[2]) ? cleanText($matches[2]) : null;
    $unit = isset($matches[3]) ? cleanText($matches[3]) : null;

}

$result = [
    'title' => $title ?? null,
    'quantity' => $quantity ?? null,
    'unit' => $unit ?? null,
];

var_dump($result);
