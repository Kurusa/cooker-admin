<?php

namespace App\DTO;

use App\Services\Parsers\Formatters\CleanText;
use Spatie\LaravelData\Data;

class IngredientDTO extends Data
{
    public function __construct(
        public string  $title,
        public ?float  $quantity = null,
        public ?string $unit = null,
        public ?string $originalTitle = null,
    )
    {
        $this->title = CleanText::cleanText($this->title);
    }
}
