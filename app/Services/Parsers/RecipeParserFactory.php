<?php

namespace App\Services\Parsers;

use App\Exceptions\UnknownSourceException;
use App\Services\Parsers\Contracts\RecipeParserInterface;
use InvalidArgumentException;

class RecipeParserFactory
{
    protected array $parsers = [];

    public function registerParser(string $sourceTitle, string $parserClass): void
    {
        if (!is_subclass_of($parserClass, RecipeParserInterface::class)) {
            throw new InvalidArgumentException('Parser class must implement RecipeParserInterface');
        }

        $this->parsers[$sourceTitle] = $parserClass;
    }

    /**
     * @throws UnknownSourceException
     */
    public function make(string $sourceTitle): RecipeParserInterface
    {
        if (!isset($this->parsers[$sourceTitle])) {
            throw new UnknownSourceException();
        }

        return new $this->parsers[$sourceTitle];
    }
}
