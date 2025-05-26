<?php

namespace App\Services\Parsers;

use App\Enums\Source\SourceRecipeUrlExcludedRuleType;
use App\Exceptions\RecipeBlockNotFoundException;
use App\Models\Source\SourceRecipeUrlExcludedRule;
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
    public function getCleanHtml(string $url): string
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

        return $cleanHtml;
    }

    public function isExcluded(string $url, int $sourceId): bool
    {
        $rules = SourceRecipeUrlExcludedRule::query()
            ->where('source_id', $sourceId)
            ->get();

        /** @var SourceRecipeUrlExcludedRule $rule */
        foreach ($rules as $rule) {
            if (
                ($rule->rule_type === SourceRecipeUrlExcludedRuleType::EXACT && $url === $rule->value) ||
                ($rule->rule_type === SourceRecipeUrlExcludedRuleType::CONTAINS && str_contains($url, $rule->value))
            ) {
                return true;
            }
        }

        if ($this->isExcludedByCategory($url)) {
            return true;
        }

        return false;
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
