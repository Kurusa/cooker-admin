<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class IngredientDTO extends Data
{
    public function __construct(
        public string  $title,
        public ?float  $quantity = null,
        public ?string $unit = null,
    )
    {
        $this->quantity = ($this->quantity && $this->unit) ? $this->quantity : null;
    }
}
