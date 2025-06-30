<?php

namespace App\Services\RecipeAttributes;

use App\DTO\RecipeCuisineDTO;
use App\Models\Recipe\Recipe;
use App\Models\Recipe\RecipeCuisine;

class CuisineService
{
    /**
     * @param RecipeCuisineDTO[] $cuisines
     * @param Recipe $recipe
     */
    public function attachCuisines(array $cuisines, Recipe $recipe): void
    {
        foreach ($cuisines as $cuisineData) {
            $cuisine = RecipeCuisine::find($cuisineData->title);

            if ($cuisine) {
                $recipe->cuisines()->attach($cuisine->id);
            }
        }
    }
}
