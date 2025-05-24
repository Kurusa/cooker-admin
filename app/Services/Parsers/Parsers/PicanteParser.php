<?php

namespace App\Services\Parsers\Parsers;

use App\Enums\Recipe\Complexity;
use App\Services\DeepseekService;
use App\Services\Parsers\BaseRecipeParser;
use DOMNode;

class PicanteParser extends BaseRecipeParser
{
    public function parseTitle(): string
    {
        return $this->xpathService->extractCleanSingleValue("//h1[@class='fn']");
    }

    public function parseCategories(): array
    {
        return $this->xpathService->extractCleanSingleValue("//ol[@id='wo-breadcrumbs']/li[3]/a/span/text()");
    }

    public function parseComplexity(): Complexity
    {
        return Complexity::MEDIUM;
    }

    public function parseCookingTime(): ?int
    {
        return null;
    }

    public function parsePortions(): int
    {
        return 1;
    }

    public function parseIngredients(): array
    {
        $ingredients = [];
        $ingredientNodes = $this->xpath->query("//ul[@class='ingredients']/li");

        foreach ($ingredientNodes as $node) {
            $name = $this->xpath->query("//span[@class='name']", $node)->item(0)->textContent;
            $amount = $this->xpath->query("//span[@class='amount']", $node)->item(0)->textContent;

            $ingredients[] = $amount ? "{$name}: {$amount}" : $name;
        }

        $service = app(DeepseekService::class);
        return $service->parseIngredients($ingredients);
    }

    public function parseSteps(): array
    {
        $steps = [];
        $paragraphNodes = $this->xpath->query("//section[@class='instructions ck-content']/p");

        /** @var DOMNode $paragraphNode */
        foreach ($paragraphNodes as $paragraphNode) {
            $description = $paragraphNode->textContent;
            if (preg_match('/^\d+\)/', $description)) {
                $description = preg_replace('/^\d+\)\s*/', '', $description);
            }

            if ($description) {
                $nextSibling = $this->xpath->query("following-sibling::p[1]/figure", $paragraphNode);
                $imageUrl = '';
                if ($nextSibling->length > 0) {
                    $imgNode = $this->xpath->query("//img/@src", $nextSibling->item(0));
                    $imageUrl = $imgNode->item(0)->nodeValue;
                }

                $steps[] = [
                    'description' => $description,
                    'imageUrl' => 'https://picantecooking.com' . $imageUrl,
                ];
            }
        }

        return $steps;
    }

    public function parseImage(): string
    {
        $imageNode = $this->xpath->query("//img[@class='img-fluid photo result-photo']")->item(0);
        return $imageNode?->getAttribute('src') ?? '';
    }

    public function urlRule(string $url): bool
    {
        return str_contains($url, 'uk/recipes');
    }
}
