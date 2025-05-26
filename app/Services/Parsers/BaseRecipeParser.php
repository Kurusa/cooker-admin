<?php

namespace App\Services\Parsers;

use App\Exceptions\RecipeBlockNotFoundException;
use App\Services\DeepseekService;
use App\Services\Parsers\Contracts\HtmlCleanerInterface;
use App\Services\Parsers\Contracts\RecipeParserInterface;
use DOMDocument;
use DOMXPath;

abstract class BaseRecipeParser implements RecipeParserInterface
{
    protected DOMXPath $xpath;

    public function __construct(
        private readonly HtmlCleanerInterface $htmlCleaner,
    )
    {
    }

    /**
     * @throws RecipeBlockNotFoundException
     */
    public function parseRecipes(string $url): array
    {
        $html = file_get_contents($url);

        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        $dom->loadHTML($html);
        $this->xpath = new DOMXPath($dom);

        $recipeNode = $this->extractRecipeNode();
        $cleanHtml = $this->htmlCleaner->cleanup($recipeNode);

        if (strlen($cleanHtml) <= 100) {
            throw new RecipeBlockNotFoundException();
        }

        $sourceKey = $this->getSourceKey();
        $this->saveDebugHtml($cleanHtml, $sourceKey, $url);

        /** @var DeepseekService $service */
        $service = app(DeepseekService::class);
        return $service->parseRecipeFromHtml($cleanHtml);
    }

    protected function saveDebugHtml(
        string $html,
        string $sourceKey,
        string $url,
    ): void
    {
        $dir = storage_path("app/parsed-html/{$sourceKey}");
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $filename = $dir . '/' . md5($url) . '.html';
        file_put_contents($filename, $html);
    }
}
