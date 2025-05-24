<?php

namespace App\Services\Parsers;

use App\DTO\RecipeDTO;
use App\Enums\Recipe\Complexity;
use App\Services\Parsers\Contracts\RecipeParserInterface;
use App\Services\XPathService;
use DOMDocument;
use DOMXPath;

abstract class BaseRecipeParser implements RecipeParserInterface
{
    protected DOMXPath $xpath;

    protected XPathService $xpathService;

    abstract public function urlRule(string $url): bool;

    public function loadHtml(string $url = null, string $html = null): void
    {
        $html = $html ?: file_get_contents($url);

        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        $dom->loadHTML($html);

        $this->xpath = new DOMXPath($dom);

        $this->xpathService = app(XPathService::class);
        $this->xpathService->setupXpath($this->xpath);
    }

    public function parseRecipes(bool $debug = false): array
    {
        return [
            new RecipeDTO(
                title: $this->parseTitle(),
                complexity: $this->parseComplexity(),
                time: $this->parseCookingTime(),
                portions: $this->parsePortions(),
                imageUrl: $this->parseImage(),
                source_recipe_url_id: null,
                categories: $this->parseCategories(),
                ingredients: $this->parseIngredients($debug),
                steps: $this->parseSteps($debug),
            ),
        ];
    }

    public function parseImage(): string
    {
        return $this->xpathService->extractSingleMetaAttribute('og:image');
    }

    public function parseTitle(): string
    {
        return $this->xpathService->extractSingleMetaAttribute('og:title');
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
}
