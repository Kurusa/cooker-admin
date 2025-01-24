<?php

namespace App\DTO;

use App\Enums\Recipe\Complexity;
use Spatie\LaravelData\Data;

class RecipeDTO extends Data
{
    public function __construct(
        public string $title,
        public Complexity $complexity,
        public ?int $time,
        public int $portions,
        public string $imageUrl,
        /** @var array<CategoryDTO> */
        public array $categories,
        /** @var array<IngredientDTO> */
        public array $ingredients,
        /** @var array<StepDTO> */
        public array $steps,
    )
    {
    }
}
