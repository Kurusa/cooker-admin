<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\IngredientUnit;
use App\Models\Recipe;
use App\Models\RecipeIngredient;
use App\Models\Source;
use App\Models\Step;
use App\Models\Unit;
use App\Services\Parsers\Contracts\RecipeParserInterface;
use App\Services\Parsers\RecipeParserFactory;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ParseRecipesCommand extends Command
{
    protected $signature = 'parse:recipes {source?} {recipeId?} {recipeUrl?}';

    protected $description = 'Parse recipes from a specific source';

    public function handle(): void
    {
        $source = $this->defineSource();

        $parser = RecipeParserFactory::make($source->title);

        $urls = $this->defineUrlsToParse($parser, $source);

        $progressBar = $this->output->createProgressBar(count($urls));
        $progressBar->start();

        foreach ($urls as $url) {
            $this->info(PHP_EOL . "Processing: $url");

            try {
                $xpath = $parser->loadHtml($url);

                DB::transaction(function () use ($url, $parser, $xpath, $source, $progressBar) {
                    DB::statement('SELECT GET_LOCK(?, -1)', ['parse_recipe_lock']);

                    $timeStart = microtime(true);

                    $category = Category::firstOrCreate(['title' => $parser->parseCategory($xpath)]);

                    $title = $parser->parseTitle($xpath);
                    if (!mb_strlen($title)) {
                        $this->error("Title can't be empty");
                        return;
                    }

                    $steps = $parser->parseSteps($xpath);
                    if (!count($steps)) {
                        $this->error("Steps can't be empty");
                        return;
                    }

                    $ingredients = $parser->parseIngredients($xpath);
                    if (!count($ingredients)) {
                        $this->error("Ingredients can't be empty");
                        return;
                    }

                    /** @var Recipe $recipe */
                    $recipe = Recipe::updateOrCreate([
                        'id' => $this->argument('recipeId'),
                    ], [
                        'title' => $title,
                        'complexity' => $parser->parseComplexity($xpath),
                        'time' => $parser->parseCookingTime($xpath),
                        'portions' => $parser->parsePortions($xpath),
                        'source_url' => $url,
                        'source_id' => $source->id,
                        'category_id' => $category->id,
                        'image_url' => $parser->parseImage($xpath),
                    ]);

                    if (!$this->argument('recipeId')) {
                        $this->attachStepsToRecipe($steps, $recipe);

                        $this->attachIngredientsToRecipe($ingredients, $recipe);

                        $this->info("Recipe saved: {$recipe->title}");
                    }

                    if ($this->argument('recipeId')) {
                        $recipe->steps()->delete();
                        $this->attachStepsToRecipe($steps, $recipe);

                        RecipeIngredient::where('recipe_id', $recipe->id)->delete();
                        $this->attachIngredientsToRecipe($ingredients, $recipe);
                    }

                    $this->info('Execution time: ' . (microtime(true) - $timeStart) . 'sec');
                    $progressBar->advance();
                });
            } catch (Exception $e) {
                $this->error("Failed to save recipe: {$e->getMessage()}. Url: {$url}");
                continue;
            } finally {
                DB::statement('SELECT RELEASE_LOCK(?)', ['parse_recipe_lock']);
            }
        }

        $progressBar->finish();
    }

    private function attachStepsToRecipe(array $steps, Recipe $recipe): void
    {
        foreach ($steps as $step) {
            if (!$step || (isset($step['description']) && !mb_strlen($step['description']))) {
                continue;
            }

            Step::create([
                'recipe_id' => $recipe->id,
                'description' => $step['description'] ?? $step,
                'image_url' => $step['imageUrl'] ?? '',
            ]);
        }
    }

    private function attachIngredientsToRecipe(array $ingredients, Recipe $recipe): void
    {
        foreach ($ingredients as $ingredientData) {
            if (!strlen($ingredientData['title'])) {
                continue;
            }

            /** @var Ingredient $ingredient */
            $ingredient = Ingredient::firstOrCreate(['title' => $ingredientData['title']]);

            $unit = null;
            if (!empty($ingredientData['unit'])) {
                /** @var Unit $unit */
                $unit = Unit::firstOrCreate(['title' => $ingredientData['unit']]);
            }

            /** @var IngredientUnit $ingredientUnit */
            $ingredientUnit = IngredientUnit::firstOrCreate([
                'ingredient_id' => $ingredient->id,
                'unit_id' => $unit?->id,
            ]);

            $recipe->ingredients()->attach($ingredientUnit->id, [
                'quantity' => $ingredientData['quantity'] ?? null,
            ]);
        }
    }

    private function defineSource(): ?Source
    {
        if ($this->argument('source')) {
            return Source::where('title', $this->argument('source'))->first();
        }

        if ($this->argument('recipeId')) {
            /** @var Recipe $recipe */
            $recipe = Recipe::find($this->argument('recipeId'));

            return $recipe->source;
        }

        if ($this->argument('recipeUrl')) {
            /** @var Recipe $recipe */
            $recipe = Recipe::where($this->argument('recipeUrl'))->first();

            return $recipe->source;
        }

        return null;
    }

    private function defineUrlsToParse(
        RecipeParserInterface $parser,
        Source $source,
    ): array
    {
        if ($recipeId = $this->argument('recipeId')) {
            /** @var Recipe $recipe */
            $recipe = Recipe::find($recipeId);
            return [$recipe->source_url];
        } elseif ($url = $this->argument('recipeUrl')) {
            return [$url];
        } else {
            return $parser->getFilteredSitemapUrls($source);
        }
    }
}
