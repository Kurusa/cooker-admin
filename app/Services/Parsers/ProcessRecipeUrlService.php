<?php

namespace App\Services\Parsers;

use App\Exceptions\RecipeAttributeCantBeEmptyException;
use App\Models\SourceRecipeUrl;
use App\Services\Parsers\Contracts\RecipeParserInterface;
use App\Services\RecipeAttributes\IngredientService;
use App\Services\RecipeAttributes\RecipeService;
use App\Services\RecipeAttributes\StepService;
use Illuminate\Support\Facades\DB;

class ProcessRecipeUrlService
{
    public function __construct(
        private readonly RecipeService $recipeService,
        private readonly IngredientService $ingredientService,
        private readonly StepService $stepService,
    )
    {
    }

    public function processRecipeUrl(
        SourceRecipeUrl $sourceRecipeUrl,
        RecipeParserInterface $parser,
    ): void
    {
        try {
            $xpath = $parser->loadHtml($sourceRecipeUrl->url);

            DB::transaction(function () use ($sourceRecipeUrl, $parser, $xpath) {
                DB::statement('SELECT GET_LOCK(?, -1)', ['parse_recipe_lock']);

                $title = $parser->parseTitle($xpath);
                if (!mb_strlen($title)) {
                    throw new RecipeAttributeCantBeEmptyException("Title can't be empty");
                }

                $steps = $parser->parseSteps($xpath);
                if (!count($steps)) {
                    throw new RecipeAttributeCantBeEmptyException("Steps can't be empty");
                }

                $ingredients = $parser->parseIngredients($xpath);
                if (!count($ingredients)) {
                    throw new RecipeAttributeCantBeEmptyException("Ingredients can't be empty");
                }

                $recipe = $this->recipeService->createOrUpdateRecipe([
                    'source_recipe_url_id' => $sourceRecipeUrl->id,
                    'category' => $parser->parseCategory($xpath),
                    'title' => $title,
                    'complexity' => $parser->parseComplexity($xpath),
                    'time' => $parser->parseCookingTime($xpath),
                    'portions' => $parser->parsePortions($xpath),
                    'image_url' => $parser->parseImage($xpath),
                ]);

                $this->stepService->attachSteps($steps, $recipe);
                $this->ingredientService->attachIngredients($ingredients, $recipe);
            });
        } finally {
            DB::statement('SELECT RELEASE_LOCK(?)', ['parse_recipe_lock']);
        }
    }
}
