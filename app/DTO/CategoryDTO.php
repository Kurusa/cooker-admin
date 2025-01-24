<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class CategoryDTO extends Data
{
    public function __construct(
        public string $title,
    )
    {
    }
}
