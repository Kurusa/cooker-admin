<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\IngredientUnit;
use App\Models\Recipe;
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

        $urls = $this->defineUrlsToParse($parser, $source->sitemap_url);

        foreach ($urls as $url) {
            if (
                (
                    !$this->argument('recipeId') &&
                    !$this->argument('recipeUrl')
                ) &&
                Recipe::where('source_url', $url)->exists()) {
                continue;
            }

            $this->info("Processing: $url");

            try {
                $xpath = $parser->loadHtml($url);

                DB::transaction(function () use ($url, $parser, $xpath, $source) {
                    $category = Category::firstOrCreate(['title' => $parser->parseCategory($xpath)]);

                    $title = $parser->parseTitle($xpath);
                    $steps = $parser->parseSteps($xpath);
                    $ingredients = $parser->parseIngredients($xpath);

//                    dd([
//                        'title' => $title,
//                        'complexity' => $parser->parseComplexity($xpath),
//                        'time' => $parser->parseCookingTime($xpath),
//                        'portions' => $parser->parsePortions($xpath),
//                        'source_url' => $url,
//                        'source_id' => $source->id,
//                        'category_id' => $category->id,
//                        'image_url' => $parser->parseImage($xpath),
//                        'ingredients' => $ingredients,
//                        'steps' => $steps,
//                    ]);

                    if (!mb_strlen($title)) {
                        $this->error("Title can't be empty");
                        return;
                    }

                    if (!count($steps)) {
                        $this->error("Steps can't be empty");
                        return;
                    }

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
                });
            } catch (Exception $e) {
                $this->error("Failed to save recipe: {$e->getMessage()} {$e->getTraceAsString()}. Url: {$url}");
                continue;
            }
        }
    }

    private function attachStepsToRecipe(array $steps, Recipe $recipe): void
    {
        foreach ($steps as $step) {
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
        string $sitemapUrl,
    ): array
    {
        $urls = $parser->getSitemapUrls($sitemapUrl);

        if ($recipeId = $this->argument('recipeId')) {
            /** @var Recipe $recipe */
            $recipe = Recipe::find($recipeId);
            $urls = [$recipe->source_url];
        }

        if ($url = $this->argument('recipeUrl')) {
            $urls = [$url];
        }

        return $urls;
    }
}
