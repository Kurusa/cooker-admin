<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class StepDTO extends Data
{
    public function __construct(
        public string $description,
        public string $image = '',
    )
    {
    }
}
