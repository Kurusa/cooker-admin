<?php

namespace App\Services\Parsers\Formatters;

class IngredientFormatter
{
    public static function formatIngredient(string $ingredient): array
    {
        $pattern = '/^(?:(\d+[.,]?\d*|\d+\/\d+)\s*([^\d\s]+)\s+(.+)|(.+?)\s*[-–—:]\s*(\d+[.,]?\d*|\d+\/\d+)?\s*([^\d\s]+)?)$/u';

        $title = $ingredient;
        $quantity = null;
        $unit = null;

        if (preg_match($pattern, $ingredient, $matches)) {
            if (!empty($matches[1]) && !empty($matches[2]) && !empty($matches[3])) {
                $quantity = self::processQuantity($matches[1]);
                $unit = self::translateUnit($matches[2]) ?? null;
                $title = $matches[3];
            } else {
                $title = $matches[4] ?? '';
                $quantity = self::processQuantity($matches[5]) ?? null;
                $unit = self::translateUnit($matches[6]) ?? null;
            }
        }

        return [
            'title' => CleanText::cleanText($title ?? ''),
            'quantity' => CleanText::cleanText($quantity ?? ''),
            'unit' => CleanText::cleanText($unit ?? ''),
        ];
    }

    public static function translateUnit(?string $unit): ?string
    {
        $translations = [
            'kg' => 'кг',
            'g' => 'г',
            'cups' => 'склянки',
            'tbsp' => 'ст. л',
            'tsp' => 'ч. л',
            'ml' => 'мл',
            'l' => 'л',
            'qt' => 'шт',
            'mg' => 'мг',
        ];

        return $translations[$unit] ?? $unit;
    }

    private static function processQuantity(?string $quantity): ?string
    {
        $cleanQuantity = CleanText::cleanQuantity(CleanText::cleanText($quantity));
        if (str_contains($cleanQuantity, '/')) {
            $numbers = explode('/', $cleanQuantity);
            $cleanQuantity = round($numbers[0] / $numbers[1], 6);
        }

        return $cleanQuantity == intval($cleanQuantity) ? intval($cleanQuantity) : $cleanQuantity;
    }
}
