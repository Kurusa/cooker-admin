<?php

namespace App\Services;

use DOMAttr;
use DOMElement;
use DOMNode;
use DOMText;
use DOMXPath;

class XPathService
{
    private DOMXPath $xpath;

    public function setupXpath(DomXPath $xpath): void
    {
        $this->xpath = $xpath;
    }

    public function extractCleanSingleValue(string $query, ?DOMNode $contextNode = null): string
    {
        return mb_strtolower($this->xpath
            ->query($query, $contextNode)
            ->item(0)
            ?->nodeValue) ?? '';
    }

    public function extractMultipleValues(string $query, bool $isMetaAttribute = false): array
    {
        $nodes = $this->xpath->query($query . ($isMetaAttribute ? '/@content' : ''));

        return array_map(fn(DOMElement|DOMAttr|DOMText $node) => $node->nodeValue, iterator_to_array($nodes));
    }

    public function extractSingleMetaAttribute(string $propertyName): string
    {
        return $this->xpath->query("//meta[@property='$propertyName']")
            ->item(0)
            ?->getAttribute('content')
            ?? '';
    }
}
