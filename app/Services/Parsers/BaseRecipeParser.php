<?php

namespace App\Services\Parsers;

use App\Enums\Source\SourceRecipeUrlExcludedRuleTypeEnum;
use App\Exceptions\RecipeBlockNotFoundException;
use App\Models\Source\SourceRecipeUrl;
use App\Models\Source\SourceRecipeUrlExcludedRule;
use App\Services\Parsers\Contracts\HtmlCleanerInterface;
use App\Services\Parsers\Contracts\RecipeParserInterface;
use DOMDocument;
use DOMXPath;
use Exception;
use Illuminate\Support\Facades\Log;

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
        dd($cleanHtml);
        Log::channel('parser_log')->info(now() . ' | ' . strlen($cleanHtml) . ' characters. url: ' . $url);

        return $cleanHtml;
    }

    public function isExcluded(SourceRecipeUrl $sourceRecipeUrl): bool
    {
        $rules = $sourceRecipeUrl->source->excludedRules;

        /** @var SourceRecipeUrlExcludedRule $rule */
        foreach ($rules as $rule) {
            if (
                ($rule->rule_type === SourceRecipeUrlExcludedRuleTypeEnum::EXACT && $sourceRecipeUrl->url === $rule->value) ||
                ($rule->rule_type === SourceRecipeUrlExcludedRuleTypeEnum::CONTAINS && str_contains($sourceRecipeUrl->url, $rule->value)) ||
                ($rule->rule_type === SourceRecipeUrlExcludedRuleTypeEnum::NOT_CONTAINS && !str_contains($sourceRecipeUrl->url, $rule->value)) ||
                ($rule->rule_type === SourceRecipeUrlExcludedRuleTypeEnum::REGEX && !preg_match($rule->value, $sourceRecipeUrl->url))
            ) {
                return true;
            }
        }

        if ($this->isExcludedByCategory($sourceRecipeUrl->url)) {
            return true;
        }

        try {
            file_get_contents($sourceRecipeUrl->url);
        } catch (Exception $e) {
            if (str_contains($e->getMessage(), '404 Not Found')) {
                return true;
            }
        }

        return false;
    }
}
