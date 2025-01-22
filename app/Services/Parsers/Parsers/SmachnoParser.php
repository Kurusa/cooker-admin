<?php

namespace App\Services\Parsers\Parsers;

use App\Enums\Recipe\Complexity;
use App\Services\DeepseekService;
use App\Services\Parsers\BaseRecipeParser;
use App\Services\Parsers\Formatters\CleanText;
use DOMXPath;

class SmachnoParser extends BaseRecipeParser
{
    public function parseTitle(DOMXPath $xpath): string
    {
        return $this->extractCleanSingleValue($xpath, "//h1[@itemprop='name']");
    }

    public function parseCategory(DOMXPath $xpath): string
    {
        return '';
    }

    public function parseComplexity(DOMXPath $xpath): Complexity
    {
        return Complexity::MEDIUM;
    }

    public function parseCookingTime(DOMXPath $xpath): ?int
    {
        return null;
    }

    public function parsePortions(DOMXPath $xpath): int
    {
        return 1;
    }

    public function parseIngredients(DOMXPath $xpath, bool $debug = false): array
    {
        $ingredients = [];
        $ingredientNodes = $xpath->query("//span[@itemprop='ingredient']/ul/li");

        foreach ($ingredientNodes as $node) {
            $nameNode = $xpath->query("//span[@itemprop='name']", $node);
            $amountNode = $xpath->query("//span[@itemprop='amount']", $node);

            $name = trim($nameNode->item(0)?->textContent ?? '');
            $amount = trim($amountNode->item(0)?->textContent ?? '');

            $ingredients[] = $amount ? "{$name}: {$amount}" : $name;
        }

        $service = app(DeepseekService::class);
        return $service->parseIngredients($ingredients);
    }

    public function parseSteps(DOMXPath $xpath): array
    {
        $steps = [];
        $stepNodes = $xpath->query("//div[@itemprop='instructions']/div[@class='step']");

        foreach ($stepNodes as $node) {
            $descriptionNode = $xpath->query("//div[@class='step_text']", $node);
            $imageNode = $xpath->query("//img/@src", $node);

            $description = trim($descriptionNode->item(0)?->textContent ?? '');
            $image = trim($imageNode->item(0)?->nodeValue ?? '');

            $steps[] = [
                'description' => CleanText::cleanText($description),
                'imageUrl' => $image ? 'https://www.smachno.in.ua/' . $image : '',
            ];
        }

        return $steps;
    }

    public function parseImage(DOMXPath $xpath): string
    {
        $imageNode = $xpath->query("//img[@itemprop='photo']")->item(0);

        if ($src = $imageNode?->getAttribute('src')) {
            return 'https://www.smachno.in.ua/' . $src;
        }

        return '';
    }

    public function urlRule(string $url): bool
    {
        return !str_contains($url, 'in.ua/ru/') && str_contains($url, '?id=');
    }
}
