<?php

namespace App\DTO;

use App\Enums\Recipe\Complexity;
use App\Services\Parsers\Formatters\CleanText;
use Spatie\LaravelData\Data;

class RecipeDTO extends Data
{
    public function __construct(
        public string     $title,
        public Complexity $complexity,
        public ?int       $time = null,
        public int        $portions = 1,
        public string     $imageUrl,
        public ?int       $source_recipe_url_id = null,
        /** @var array<CuisineDTO> */
        public array      $cuisines,
        /** @var array<CategoryDTO> */
        public array      $categories,
        /** @var array<IngredientDTO> */
        public array      $ingredients,
        /** @var array<StepDTO> */
        public array      $steps,
    )
    {
        $this->title = CleanText::cleanText($this->title);
    }
}
