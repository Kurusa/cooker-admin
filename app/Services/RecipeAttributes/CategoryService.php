<?php

namespace App\Services\RecipeAttributes;

use App\DTO\RecipeCategoryDTO;
use App\Models\Recipe\Recipe;
use App\Models\Recipe\RecipeCategory;

class CategoryService
{
    /**
     * @param RecipeCategoryDTO[] $categories
     * @param Recipe $recipe
     */
    public function attachCategories(array $categories, Recipe $recipe): void
    {
        foreach ($categories as $categoryData) {
            /** @var RecipeCategory $category */
            $category = RecipeCategory::where('title', $categoryData->title)->first();

            $recipe->categories()->syncWithoutDetaching([$category->id]);
        }
    }
}
