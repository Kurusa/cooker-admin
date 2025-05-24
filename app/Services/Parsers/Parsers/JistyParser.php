<?php

namespace App\Services\Parsers\Parsers;

use App\DTO\CategoryDTO;
use App\DTO\IngredientDTO;
use App\DTO\RecipeDTO;
use App\DTO\StepDTO;
use App\Services\DeepseekService;
use App\Services\Parsers\BaseRecipeParser;
use DOMElement;
use DOMNode;
use DOMNodeList;

class JistyParser extends BaseRecipeParser
{
    private const DOMAIN = 'https://jisty.com.ua';

    public function parseRecipes(bool $debug = false): array
    {
        $recipeNodes = $this->xpath->query("//h3[contains(@class, 'has-text-align-center wp-block-heading')]");

        if ($recipeNodes->length <= 1) {
            return [
                new RecipeDTO(
                    title: $this->parseTitle(),
                    complexity: $this->parseComplexity(),
                    time: $this->parseCookingTime(),
                    portions: $this->parsePortions(),
                    imageUrl: $this->parseImage(),
                    categories: $this->parseCategories(),
                    ingredients: $this->parseIngredients($debug),
                    steps: $this->parseSteps($debug),
                    source_recipe_url_id: null,
                ),
            ];
        }

        return $this->parseMultipleRecipes($recipeNodes, $debug);
    }

    public function parseCategories(): array
    {
        return [
            new CategoryDTO(
                title: $this->xpathService->extractCleanSingleValue("//span[@class='post-cat bg-warning']")
            ),
        ];
    }

    public function parseIngredients(): array
    {
        $ingredientNodes = $this->xpath->query(".//div[@class='wp-block-wpzoom-recipe-card-block-ingredients'][1]/ul[@class='ingredients-list']/li");

        return $this->formatIngredients($ingredientNodes, $debug);
    }

    public function parseSteps(): array
    {
        $stepNodes = $this->xpath->query("//div[@class='wp-block-wpzoom-recipe-card-block-directions'][1]/ul[@class='directions-list']/li[@class='direction-step']");

        return $this->formatSteps($stepNodes);
    }

    public function urlRule(string $url): bool
    {
        $disallowedPatterns = [
            '/blog',
            '/top-',
            '-faktiv-',
            '-pravil-',
            '-productiv-',
            'restoran-',
            'kuhar',
            'najsmachnishih-retseptiv-mlintsiv',
            'yak-pr',
            'najkrash'
        ];

        foreach ($disallowedPatterns as $pattern) {
            if (str_contains($url, $pattern)) {
                return false;
            }
        }

        return true;
    }

    private function formatIngredients(DOMNodeList $ingredientNodes, bool $debug = false): array
    {
        $ingredients = [];
        /** @var DOMElement $ingredient */
        foreach ($ingredientNodes as $ingredient) {
            $ingredient = str_replace([
                '(adsbygoogle=window.adsbygoogle||[]).push({})',
                '(adsbygoogle = window.adsbygoogle || []).push({})',
                'спеції:',
            ], '', $ingredient->textContent);

            $ingredients[] = new IngredientDTO(
                title: $ingredient
            );
        }

        return app(DeepseekService::class)->parseIngredients($ingredients);
    }

    private function formatSteps(DOMNodeList $stepNodes): array
    {
        $steps = [];

        /** @var DOMNode $stepNode */
        foreach ($stepNodes as $stepNode) {
            $imageNode = $this->xpath->query("//img", $stepNode);
            $imageUrl = ($src = $imageNode
                ->item(0)
                ?->getAttribute('data-src')
            )
                ? self::DOMAIN . $src
                : '';

            $description = $stepNode->textContent;
            if (!in_array($description, array_column($steps, 'description'))) {
                $steps[] = new StepDTO(
                    description: $description,
                    image: $imageUrl,
                );
            }
        }

        return $steps;
    }

    private function parseMultipleRecipes(DOMNodeList $recipeNodes, bool $debug = false): array
    {
        $recipes = [];
        foreach ($recipeNodes as $index => $recipeNode) {
            $imageNode = $this->xpath->query("//figure[@class='aligncenter size-large is-resized'][$index+1]/img");
            $imageUrl = ($src = $imageNode
                ->item(0)
                ?->getAttribute('src')
            )
                ? self::DOMAIN . $src
                : '';

            $ingredientNodes = $this->xpath->query(".//div[contains(@class, 'wp-block-wpzoom-recipe-card-block-ingredients')][$index+1]/ul[@class='ingredients-list']/li");
            $ingredients = $this->formatIngredients($ingredientNodes, $debug);

            $stepNodes = $this->xpath->query(".//div[contains(@class, 'wp-block-wpzoom-recipe-card-block-directions')][$index+1]/ul[@class='directions-list']/li");
            $steps = $this->formatSteps($stepNodes);

            $recipes[] = new RecipeDTO(
                title: $recipeNode->textContent,
                complexity: $this->parseComplexity(),
                time: $this->parseCookingTime(),
                portions: $this->parsePortions(),
                imageUrl: $imageUrl,
                categories: $this->parseCategories(),
                ingredients: $debug ? $ingredients : app(DeepseekService::class)->parseIngredients($ingredients),
                steps: $steps,
                source_recipe_url_id: null,
            );
        }

        return $recipes;
    }
}
