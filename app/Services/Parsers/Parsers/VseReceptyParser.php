<?php

namespace App\Services\Parsers\Parsers;

use App\Services\Parsers\BaseRecipeParser;
use DOMDocument;
use DOMNode;
use DOMXPath;
use Exception;
use Illuminate\Support\Facades\Log;

class VseReceptyParser extends BaseRecipeParser
{
    public function extractRecipeNode(): DOMNode
    {
        $recipeNode = $this->xpath->query("//div[contains(@class, 'item-description')]")->item(0);

        $span = $this->dom->createElement('span', 'Мій текст');

        $recipeNode->appendChild(
            $this->xpath->query("//img[contains(@class, 'attachment-post-thumbnail')]")->item(0)
        );

        return $recipeNode;
    }

    public function isExcludedByCategory(string $url): bool
    {
        try {
            $html = file_get_contents($url);
        } catch (Exception $e) {
            Log::info('isExcludedByCategory error: ' . $e->getMessage() . '. url: ' . $url);
            return true;
        }

        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        $dom->loadHTML($html);
        $xpath = new DOMXPath($dom);

        $descriptionDiv = $xpath->query("//div[contains(@class, 'item-description')]")->item(0);

        if ($descriptionDiv) {
            $paragraphs = $xpath->query('./p', $descriptionDiv);

            if ($paragraphs->length === 1) {
                return true;
            }
        }

        $stepsDiv = $xpath->query("//div[contains(@class, 'recipe-steps')]")->length;
        if (!$stepsDiv) {
            return true;
        }

        return false;
    }
}
