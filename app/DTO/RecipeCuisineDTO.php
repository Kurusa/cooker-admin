<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class RecipeCuisineDTO extends Data
{
    public function __construct(
        public string $title,
    )
    {
    }
}
