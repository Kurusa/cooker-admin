<?php

namespace App\Services;

use App\DTO\RecipeDTO;
use App\Enums\AiProvider;
use App\Exceptions\AiProviderDidntFindRecipeException;
use App\Exceptions\RecipeBlockNotFoundException;
use App\Models\Source\SourceRecipeUrl;
use App\Notifications\AiProviderDidntFindRecipeNotification;
use App\Notifications\RecipeBlockNotFoundNotification;
use App\Notifications\RecipeParsingCompleted;
use App\Services\AiProviders\AiRecipeParserResolver;
use App\Services\Parsers\Contracts\RecipeParserInterface;
use App\Services\RecipeAttributes\CategoryService;
use App\Services\RecipeAttributes\CuisineService;
use App\Services\RecipeAttributes\IngredientService;
use App\Services\RecipeAttributes\RecipeService;
use App\Services\RecipeAttributes\StepService;
use Exception;
use Illuminate\Database\ConnectionInterface as Database;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class ProcessRecipeUrlService
{
    public function __construct(
        private readonly RecipeService          $recipeService,
        private readonly IngredientService      $ingredientService,
        private readonly StepService            $stepService,
        private readonly CategoryService        $categoryService,
        private readonly CuisineService         $cuisineService,
        private readonly Database               $db,
        private readonly AiRecipeParserResolver $aiProviderResolver,
    )
    {
    }

    public function processRecipeUrl(
        SourceRecipeUrl       $sourceRecipeUrl,
        RecipeParserInterface $parser,
        AiProvider            $aiProvider,
    ): void
    {
        if ($this->shouldSkipParsing($sourceRecipeUrl)) {
            return;
        }

        $aiService = $this->aiProviderResolver->resolve($aiProvider);

        try {
            $cleanHtml = $parser->getCleanHtml($sourceRecipeUrl->url);
            $recipes = $aiService->parse($cleanHtml);
        } catch (RecipeBlockNotFoundException $exception) {
            Notification::route('telegram', config('services.telegram.chat_id'))->notify(new RecipeBlockNotFoundNotification($sourceRecipeUrl));
            return;
        } catch (AiProviderDidntFindRecipeException $exception) {
            Notification::route('telegram', config('services.telegram.chat_id'))->notify(new AiProviderDidntFindRecipeNotification(
                $sourceRecipeUrl,
                $exception->getMessage(),
            ));
            return;
        }

        /** @var RecipeDTO $recipeDTO */
        foreach ($recipes as $recipeDTO) {
            try {
                if (empty($recipeDTO->ingredientGroups) || empty($recipeDTO->steps)) {
                    continue;
                }

                $this->db->beginTransaction();

                $recipeDTO->source_recipe_url_id = $sourceRecipeUrl->id;
                $recipe = $this->recipeService->createRecipe($recipeDTO);

                $this->stepService->attachSteps($recipeDTO->steps, $recipe);
                $this->ingredientService->attachIngredientGroups($recipeDTO->ingredientGroups, $recipe);
                $this->categoryService->attachCategories($recipeDTO->categories, $recipe);
                $this->cuisineService->attachCuisines($recipeDTO->cuisines, $recipe);

                $this->db->commit();

                try {
                    Notification::route('telegram', config('services.telegram.chat_id'))->notify(new RecipeParsingCompleted($recipe));
                } catch (Exception $e) {
                    Log::error('Notification error:' . $e->getMessage());
                    continue;
                }
            } catch (Exception $exception) {
                $this->db->rollBack();
                throw $exception;
            }
        }
    }

    private function shouldSkipParsing(SourceRecipeUrl $sourceRecipeUrl): bool
    {
        $ch = curl_init($sourceRecipeUrl->url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 404) {
            $sourceRecipeUrl->update(['is_excluded' => true]);
            return true;
        }

        return $sourceRecipeUrl->is_excluded;
    }
}
