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
        $categories = collect($categories)
            ->unique(fn(RecipeCategoryDTO $category) => $category->title)
            ->filter();

        foreach ($categories as $categoryData) {
            /** @var RecipeCategory $category */
            $category = RecipeCategory::updateOrCreate([
                'title' => $categoryData->title,
            ], [
                'title' => $categoryData->title,
            ]);

            $recipe->categories()->attach($category->id);
        }
    }
}
