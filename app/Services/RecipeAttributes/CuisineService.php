<?php

namespace App\Services\RecipeAttributes;

use App\DTO\CuisineDTO;
use App\Models\Cuisine;
use App\Models\Recipe\Recipe;

class CuisineService
{
    /**
     * @param CuisineDTO[] $cuisines
     * @param Recipe $recipe
     */
    public function attachCuisines(array $cuisines, Recipe $recipe): void
    {
        $cuisines = collect($cuisines)
            ->unique(fn(CuisineDTO $cuisine) => $cuisine->title)
            ->filter();

        foreach ($cuisines as $cuisineData) {
            $cuisine = Cuisine::updateOrCreate([
                'title' => $cuisineData->title,
            ], [
                'title' => $cuisineData->title,
            ]);

            $recipe->cuisines()->attach($cuisine->id);
        }
    }
}
