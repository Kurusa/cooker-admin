<?php

namespace App\Services\RecipeAttributes;

use App\Models\Category;
use App\Models\Recipe;

class RecipeService
{
    public function createOrUpdateRecipe(array $recipeData): Recipe
    {
        /** @var Category $category */
        $category = Category::firstOrCreate(['title' => $recipeData['category']]);
        $recipeData['category_id'] = $category->id;

        return Recipe::updateOrCreate(
            ['source_recipe_url_id' => $recipeData['source_recipe_url_id'] ?? null],
            $recipeData,
        );
    }
}
