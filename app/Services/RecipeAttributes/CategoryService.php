<?php

namespace App\Services\RecipeAttributes;

use App\DTO\CategoryDTO;
use App\Models\Category;
use App\Models\Recipe;

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

        foreach ($categories as $step) {
            /** @var Category $category */
            $category = Category::updateOrCreate([
                'title' => $category->title,
            ], ['title' => $category->title]);

            $recipe->categories()->attach($category->id);
        }
    }
}
