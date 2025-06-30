<?php

namespace App\Services\Parsers\Parsers;

use App\Services\Parsers\BaseRecipeParser;
use DOMDocument;
use DOMNode;
use DOMText;

class TorchynParser extends BaseRecipeParser
{
    public function extractRecipeNode(): DOMNode
    {
        $recipe = $this->xpath->query("//meta[@property='og:title']")->item(0)?->getAttribute('content');
        $recipe .= $this->xpath->query("//meta[@property='og:description']")->item(0)?->getAttribute('content');
        $recipe .= $this->xpath->query("//div[@class='recept__sposib-prigotuvannya']")->item(0)->textContent;

        $dom = new DOMDocument();

        libxml_use_internal_errors(true); // Вимкнення помилок для "брудного" HTML
        $dom->loadHTML($recipe);
        libxml_clear_errors(); // Очищення помилок

        // Якщо твій інтерфейс (BaseRecipeParser) вимагає DOMNode,
        // а ти повертаєш DOMDocument, це все ще працюватиме,
        // оскільки DOMDocument є підкласом DOMNode.
        // Однак, для ясності, якщо далі очікується DOMDocument,
        // варто змінити сигнатуру методу на : DOMDocument
        return $dom;
    }

    public function isExcludedByCategory(string $url): bool
    {
        return false;
    }
}
