<?php

namespace App\DTO;

use App\Services\Parsers\Formatters\CleanText;
use Spatie\LaravelData\Data;

class StepDTO extends Data
{
    public function __construct(
        public string $description,
        public string $image = '',
    )
    {
        $this->description = CleanText::cleanText($this->description);
    }
}
