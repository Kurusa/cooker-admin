<?php

namespace App\Jobs;

use App\DTO\RecipeCategoryDTO;
use App\Models\Recipe\Recipe;
use App\Models\Recipe\RecipeCategory;
use App\Models\Recipe\RecipeCuisine;
use App\Services\AiProviders\DeepseekService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CategorizeRecipesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly array $recipeIds,
    )
    {
        $this->onQueue('parsing');
    }

    public function handle(DeepseekService $deepseek): void
    {
        /** @var Collection $recipes */
        $recipes = Recipe::query()
            ->with('steps')
            ->whereIn('id', $this->recipeIds)
            ->get()
            ->map(fn(Recipe $recipe) => [
                'id' => $recipe->id,
                'title' => $recipe->title,
                'ingredients' => $recipe->ingredientTitles,
                'steps' => $recipe->steps->pluck('description')->all(),
            ]);

        $results = $deepseek->categorizeRecipes($recipes);

        foreach ($results as $result) {
            $recipeId = $result['id'];
            $categoryDTOs = $result['categories'] ?? [];
            $cuisineTitles = $result['cuisines'] ?? [];

            foreach ($categoryDTOs as $dto) {
                $categoryId = $this->resolveCategory($dto);
                DB::table('recipe_categories_map')->insertOrIgnore([
                    'recipe_id' => $recipeId,
                    'category_id' => $categoryId,
                ]);
            }

            foreach ($cuisineTitles as $cuisineTitle) {
                $cuisineId = RecipeCuisine::firstOrCreate(['title' => $cuisineTitle])->id;
                DB::table('recipe_cuisines_map')->insertOrIgnore([
                    'recipe_id' => $recipeId,
                    'cuisine_id' => $cuisineId,
                ]);
            }
        }
    }

    private function resolveCategory(RecipeCategoryDTO $dto): int
    {
        $category = RecipeCategory::firstOrCreate(['title' => $dto->title]);

        foreach ($dto->parent_titles as $parentTitle) {
            $parent = RecipeCategory::firstOrCreate(['title' => $parentTitle]);

            $category->parents()->syncWithoutDetaching([$parent->id]);
        }

        return $category->id;
    }
}
