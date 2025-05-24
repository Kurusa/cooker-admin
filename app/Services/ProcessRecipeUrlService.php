<?php

namespace App\Services;

use App\DTO\RecipeDTO;
use App\Models\Source\SourceRecipeUrl;
use App\Services\Parsers\Contracts\RecipeParserInterface;
use App\Services\RecipeAttributes\CategoryService;
use App\Services\RecipeAttributes\IngredientService;
use App\Services\RecipeAttributes\RecipeService;
use App\Services\RecipeAttributes\StepService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessRecipeUrlService
{
    public function __construct(
        private readonly RecipeService     $recipeService,
        private readonly IngredientService $ingredientService,
        private readonly StepService       $stepService,
        private readonly CategoryService   $categoryService,
    )
    {
    }

    public function processRecipeUrl(
        SourceRecipeUrl       $sourceRecipeUrl,
        RecipeParserInterface $parser,
    ): void
    {
        try {
            $parser->loadHtml($sourceRecipeUrl->url);

            DB::transaction(function () use ($sourceRecipeUrl, $parser) {
                DB::statement('SELECT GET_LOCK(?, -1)', ['parse_recipe_lock']);

                $recipes = $parser->parseRecipes();

                foreach ($recipes as $recipeDTO) {
                    $recipeDTO->source_recipe_url_id = $sourceRecipeUrl->id;
                    $recipe = $this->recipeService->createOrUpdateRecipe($recipeDTO);

                    $this->stepService->attachSteps($recipeDTO->steps, $recipe);
                    $this->ingredientService->attachIngredients($recipeDTO->ingredients, $recipe);
                    $this->categoryService->attachCategories($recipeDTO->categories, $recipe);
                }
            });
        } catch (Exception $exception) {
            Log::error($exception->getMessage() . $exception->getTraceAsString());
        } finally {
            DB::statement('SELECT RELEASE_LOCK(?)', ['parse_recipe_lock']);
        }
    }
}
