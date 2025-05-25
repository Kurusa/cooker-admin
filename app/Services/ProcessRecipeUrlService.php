<?php

namespace App\Services;

use App\Exceptions\RecipeBlockNotFoundException;
use App\Models\Source\SourceRecipeUrl;
use App\Services\Parsers\Contracts\RecipeParserInterface;
use App\Services\RecipeAttributes\CategoryService;
use App\Services\RecipeAttributes\CuisineService;
use App\Services\RecipeAttributes\IngredientService;
use App\Services\RecipeAttributes\RecipeService;
use App\Services\RecipeAttributes\StepService;
use Exception;
use Illuminate\Database\ConnectionInterface as Database;
use Illuminate\Support\Facades\Log;

class ProcessRecipeUrlService
{
    public function __construct(
        private readonly RecipeService     $recipeService,
        private readonly IngredientService $ingredientService,
        private readonly StepService       $stepService,
        private readonly CategoryService   $categoryService,
        private readonly CuisineService    $cuisineService,
        private readonly Database          $db,
    )
    {
    }

    public function processRecipeUrl(
        SourceRecipeUrl       $sourceRecipeUrl,
        RecipeParserInterface $parser,
    ): void
    {
        if ($sourceRecipeUrl->is_excluded) {
            return;
        }

        try {
            $recipes = $parser->parseRecipes($sourceRecipeUrl->url);

            $this->db->beginTransaction();

            foreach ($recipes as $recipeDTO) {
                $recipeDTO->source_recipe_url_id = $sourceRecipeUrl->id;
                $recipe = $this->recipeService->createOrUpdateRecipe($recipeDTO);

                $this->stepService->attachSteps($recipeDTO->steps, $recipe);
                $this->ingredientService->attachIngredientGroups($recipeDTO->ingredientGroups, $recipe);
                $this->categoryService->attachCategories($recipeDTO->categories, $recipe);
                $this->cuisineService->attachCuisines($recipeDTO->cuisines, $recipe);
            }

            $this->db->commit();
        } catch (RecipeBlockNotFoundException $exception) {
            Log::info('Recipe block not found for ' . $sourceRecipeUrl->url);

            $this->db->rollBack();
            throw $exception;
        } catch (Exception $exception) {
            $this->db->rollBack();
            throw $exception;
        }
    }
}
