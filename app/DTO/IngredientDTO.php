<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class IngredientDTO extends Data
{
    public function __construct(
        public string $title,
        public ?float $quantity,
        public ?string $unit,
    )
    {
    }
}
