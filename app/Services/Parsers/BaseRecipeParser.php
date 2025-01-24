<?php

namespace App\Services\Parsers;

use App\DTO\RecipeDTO;
use App\Services\Parsers\Contracts\RecipeParserInterface;
use App\Services\Parsers\Formatters\CleanText;
use DOMDocument;
use DOMNode;
use DOMXPath;

abstract class BaseRecipeParser implements RecipeParserInterface
{
    abstract public function urlRule(string $url): bool;

    public function loadHtml(string $url): DOMXPath
    {
        $html = file_get_contents($url);

        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        $dom->loadHTML($html);

        //file_put_contents('storage/logs/html.html', $html);

        return new DOMXPath($dom);
    }

    protected function extractCleanSingleValue(DOMXPath $xpath, string $query, ?DOMNode $contextNode = null): string
    {
        $node = $xpath->query($query, $contextNode)->item(0);
        return $node ? CleanText::cleanText($node->nodeValue) : '';
    }

    protected function extractMultipleValues(DOMXPath $xpath, string $query, ?DOMNode $contextNode = null): array
    {
        $nodes = $xpath->query($query, $contextNode);
        $values = [];

        foreach ($nodes as $node) {
            $values[] = CleanText::cleanText($node->nodeValue);
        }

        return $values;
    }

    public function parseRecipes(DOMXPath $xpath): array
    {
        return [
            RecipeDTO::from([
                'title' => $this->parseTitle($xpath),
                'category' => $this->parseCategory($xpath),
                'complexity' => $this->parseComplexity($xpath),
                'time' => $this->parseCookingTime($xpath),
                'portions' => $this->parsePortions($xpath),
                'image_url' => $this->parseImage($xpath),
                'ingredients' => $this->parseIngredients($xpath),
                'steps' => $this->parseSteps($xpath),
            ]),
        ];
    }
}
