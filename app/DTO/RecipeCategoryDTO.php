<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class RecipeCategoryDTO extends Data
{
    public function __construct(
        public string $title,
    )
    {
    }
}
