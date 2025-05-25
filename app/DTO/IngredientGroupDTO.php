<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class IngredientGroupDTO extends Data
{
    public function __construct(
        public ?string $group,
        /** @var array<IngredientDTO> */
        public array   $ingredients,
    )
    {
    }
}
