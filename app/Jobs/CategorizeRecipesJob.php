<?php

namespace App\Jobs;

use App\DTO\RecipeCategoryDTO;
use App\DTO\RecipeCuisineDTO;
use App\Enums\Source\AiProvider;
use App\Models\Recipe\Recipe;
use App\Models\Recipe\RecipeCategory;
use App\Models\Recipe\RecipeCuisine;
use App\Services\AiProviders\AiRecipeParserResolver;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CategorizeRecipesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly Collection $recipes,
    )
    {
        $this->onQueue('parsing');
    }

    public function handle(
        AiRecipeParserResolver $aiRecipeParserResolver,
    ): void
    {
        $mapped = $this->recipes->map(function (Recipe $recipe) {
            return [
                'id' => $recipe->id,
                'title' => $recipe->title,
                'ingredients' => implode(',', array_slice($recipe->ingredientTitles, 0, 5)),
            ];
        });

        $response = $aiRecipeParserResolver
            ->resolve(AiProvider::OPENAI)
            ->categorizeRecipes($mapped);

        foreach ($response as $recipeData) {
            $recipeId = $recipeData['id'];

            $rawCategories = $recipeData['categories'] ?? [];

            $rawCuisines = $recipeData['cuisines'] ?? [];

            DB::table('recipe_categories_map')->where('recipe_id', $recipeId)->delete();
            foreach ($rawCategories as $rawCategory) {
                $categoryDto = new RecipeCategoryDTO(
                    title: $rawCategory['title'],
                    parent_titles: $rawCategory['parent_titles'] ?? []
                );

                $categoryId = $this->resolveRecipeCategory($categoryDto);

                DB::table('recipe_categories_map')->insertOrIgnore([
                    'recipe_id' => $recipeId,
                    'category_id' => $categoryId,
                ]);
            }

            DB::table('recipe_cuisines_map')->where('recipe_id', $recipeId)->delete();
            foreach ($rawCuisines as $rawCuisine) {
                $cuisineDto = new RecipeCuisineDTO(
                    title: $rawCuisine,
                );

                $cuisine = RecipeCuisine::firstOrCreate([
                    'title' => trim($cuisineDto->title),
                ]);

                DB::table('recipe_cuisines_map')->insertOrIgnore([
                    'recipe_id' => $recipeId,
                    'cuisine_id' => $cuisine->id,
                ]);
            }
        }
    }

    private function resolveRecipeCategory(RecipeCategoryDTO $recipeCategoryDto): int
    {
        $category = RecipeCategory::firstOrCreate([
            'title' => trim($recipeCategoryDto->title),
        ]);

        if (!empty($recipeCategoryDto->parent_titles)) {
            $lastParent = null;

            foreach ($recipeCategoryDto->parent_titles as $parentTitle) {
                $parentCategory = RecipeCategory::firstOrCreate([
                    'title' => trim($parentTitle),
                ]);

                $lastParent = $parentCategory;
            }

            if ($lastParent) {
                $category->parents()->syncWithoutDetaching([$lastParent->id]);
            }
        }

        return $category->id;
    }
}
