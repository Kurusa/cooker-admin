<?php

namespace App\DTO;

use App\Services\Parsers\Formatters\CleanText;
use Spatie\LaravelData\Data;

class CategoryDTO extends Data
{
    public function __construct(
        public string $title,
    )
    {
        $this->title = CleanText::cleanText($this->title);
    }
}
