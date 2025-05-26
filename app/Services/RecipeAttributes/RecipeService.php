<?php

namespace App\Services\RecipeAttributes;

use App\DTO\RecipeDTO;
use App\Models\Recipe\Recipe;

class RecipeService
{
    public function createRecipe(RecipeDTO $recipeDTO): Recipe
    {
        return Recipe::updateOrCreate(
            [
                'source_recipe_url_id' => $recipeDTO->source_recipe_url_id,
                'title' => $recipeDTO->title,
            ],
            [
                'title' => $recipeDTO->title,
                'complexity' => $recipeDTO->complexity,
                'time' => $recipeDTO->time,
                'portions' => $recipeDTO->portions,
                'image_url' => $recipeDTO->imageUrl,
            ],
        );
    }
}
