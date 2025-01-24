<?php

namespace App\Services\Parsers;

use App\DTO\RecipeDTO;
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

        //file_put_contents('storage/logs/html.html', $html);

        $this->xpath = new DOMXPath($dom);

        $this->xpathService = app(XPathService::class);
        $this->xpathService->setupXpath($this->xpath);
    }

    public function parseRecipes(): array
    {
        return [
            new RecipeDTO(
                title: $this->parseTitle(),
                complexity: $this->parseComplexity(),
                time: $this->parseCookingTime(),
                portions: $this->parsePortions(),
                imageUrl: $this->parseImage(),
                categories: $this->parseCategories(),
                ingredients: $this->parseIngredients(),
                steps: $this->parseSteps(),
            ),
        ];
    }
}
