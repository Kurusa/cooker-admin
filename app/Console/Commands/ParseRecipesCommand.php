<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\IngredientUnit;
use App\Models\Recipe;
use App\Models\Step;
use App\Models\Unit;
use App\Services\Parsers\RecipeParserFactory;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ParseRecipesCommand extends Command
{
    protected $signature = 'parse:recipes {source}';

    protected $description = 'Parse recipes from a specific source';

    public function handle(): void
    {
        $parser = RecipeParserFactory::make($this->argument('source'));

        $sourceId = $parser->getSource()->id;

        foreach ($parser->getSitemapUrls() as $url) {
            if (Recipe::where('source_url', $url)->exists()) {
                continue;
            }

            $this->info("Processing: $url");

            try {
                $xpath = $parser->loadHtml($url);

                DB::transaction(function () use ($url, $parser, $xpath, $sourceId) {
                    $category = $this->getOrCreateCategory($parser->parseCategory($xpath));

                    $title = $parser->parseTitle($xpath);
                    $steps = $parser->parseSteps($xpath);
                    $ingredients = $parser->parseIngredients($xpath);

                    if (!mb_strlen($title) || !count($steps) || !count($ingredients)) {
                        return;
                    }

                    $recipe = Recipe::create([
                        'title' => $title,
                        'complexity' => $parser->parseComplexity($xpath),
                        'time' => $parser->parseCookingTime($xpath),
                        'portions' => $parser->parsePortions($xpath),
                        'source_url' => $url,
                        'source_id' => $sourceId,
                        'category_id' => $category->id,
                        'image_url' => $parser->parseImage($xpath),
                    ]);

                    $this->attachStepsToRecipe($steps, $recipe);

                    $this->attachIngredientsToRecipe($ingredients, $recipe);

                    $this->info("Recipe saved: {$recipe->title}");
                });
            } catch (Exception $e) {
                $this->error("Failed to save recipe: {$e->getMessage()}. Url: {$url}");
                continue;
            }
        }
    }

    private function getOrCreateCategory(?string $categoryName): Category
    {
        if (!$categoryName) {
            return Category::firstOrCreate(['title' => 'новинки']);
        }

        return Category::firstOrCreate(['title' => trim($categoryName)]);
    }

    private function attachStepsToRecipe(array $steps, Recipe $recipe): void
    {
        foreach ($steps as $stepDescription) {
            Step::create([
                'recipe_id' => $recipe->id,
                'description' => $stepDescription,
            ]);
        }
    }

    private function attachIngredientsToRecipe(array $ingredients, Recipe $recipe): void
    {
        foreach ($ingredients as $ingredientData) {
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
}
