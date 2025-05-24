<?php

namespace App\Services\RecipeAttributes;

use App\DTO\CategoryDTO;
use App\Models\Category;
use App\Models\Recipe\Recipe;

class CategoryService
{
    /**
     * @param CategoryDTO[] $categories
     * @param Recipe $recipe
     */
    public function attachCategories(array $categories, Recipe $recipe): void
    {
        $categories = collect($categories)
            ->unique(fn(CategoryDTO $category) => $category->title)
            ->filter();

        foreach ($categories as $categoryData) {
            /** @var Category $category */
            $category = Category::updateOrCreate([
                'title' => $categoryData->title,
            ], [
                'title' => $categoryData->title,
            ]);

            $recipe->categories()->attach($category->id);
        }
    }
}
