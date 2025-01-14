<?php

namespace App\Console\Commands\Parsers;

use App\Enums\Recipe\Complexity;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\Step;
use DOMXPath;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use XMLReader;

class RudParser extends Command
{
    protected $signature = 'parse:rud';

    public function handle(): void
    {
        $sitemapUrl = 'https://rud.ua/sitemap.xml';
        $this->processSitemap($sitemapUrl);
    }

    private function processSitemap(string $sitemapUrl): void
    {
        $reader = new XMLReader();
        $reader->open($sitemapUrl);

        while ($reader->read()) {
            if ($reader->nodeType === XMLReader::ELEMENT && $reader->localName === 'loc') {
                $url = $reader->readString();

                if (str_starts_with($url, 'https://rud.ua/consumer/recipe/')
                    && count(explode('/', $url)) >= 8
                    && (!Recipe::where('source_url', $url)->exists())
                ) {
                    $this->processRecipePage($url);
                }
            }
        }

        $reader->close();
    }

    private function processRecipePage(string $url): void
    {
        $this->info('Обробляю ' . $url);

        $xpath = $this->loadHtml(Http::get($url)->body());

        $recipes = $this->extractRecipes($xpath, $url);

        foreach ($recipes as $recipeData) {
            $category = $this->getOrCreateCategory($recipeData['category']);

            $recipe = Recipe::create([
                'title' => $recipeData['title'],
                'complexity' => Complexity::EASY,
                'time' => $recipeData['time'],
                'image_url' => $recipeData['image'],
                'source_url' => $recipeData['source_url'],
                'category_id' => $category->id,
            ]);

            foreach ($recipeData['steps'] as $step) {
                Step::create([
                    'description' => $step,
                    'recipe_id' => $recipe->id,
                ]);
            }

            foreach ($recipeData['ingredients'] as $ingredient) {
                $ingredientDb = Ingredient::firstOrCreate([
                    'title' => $ingredient['title'],
                    'unit' => $ingredient['unit'],
                ]);

                $recipe->ingredients()->attach($ingredientDb->id);
            }
        }
    }

    private function loadHtml(string $html): DOMXPath
    {
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        return new DOMXPath($dom);
    }

    private function extractRecipes(DOMXPath $xpath, string $url): array
    {
        $recipes = [];
        $recipeNodes = $xpath->query("//div[contains(@class, 'items-recipes')]/div[contains(@class, 'item')]");

        foreach ($recipeNodes as $node) {
            $title = $this->extractSingleValue(new DOMXPath($node->ownerDocument), ".//h2[@itemprop='name']", $node);
            $time = $this->extractSingleValue(new DOMXPath($node->ownerDocument), ".//span[@class='time']/time", $node);
            $category = $this->extractSingleValue(new DOMXPath($node->ownerDocument), ".//span[@itemprop='recipeCategory']", $node);
            $image = $this->extractImage(new DOMXPath($node->ownerDocument), $node);
            $ingredients = $this->extractIngredients(new DOMXPath($node->ownerDocument), $node);
            $steps = $this->extractSteps(new DOMXPath($node->ownerDocument), $node);

            $filteredSteps = array_filter($steps, function ($step) {
                return !preg_match('/^Етап\s*№\s*\d+/u', $step);
            });

            if (!$title || !count($filteredSteps) || !count($ingredients)) {
                continue;
            }

            $recipes[] = [
                'title' => $title,
                'time' => $time,
                'category' => $category,
                'ingredients' => $ingredients,
                'steps' => array_values($filteredSteps),
                'image' => $image,
                'source_url' => $url,
            ];
        }

        return $recipes;
    }

    private function extractSingleValue(DOMXPath $xpath, string $query, ?\DOMElement $contextNode = null): ?string
    {
        $node = $contextNode
            ? $xpath->query($query, $contextNode)->item(0)
            : $xpath->query($query)->item(0);

        return $node ? trim($node->nodeValue) : null;
    }

    private function extractIngredients(DOMXPath $xpath, \DOMElement $contextNode): array
    {
        $ingredients = [];
        $ingredientNodes = $xpath->query(".//tr[@itemprop='recipeIngredient']", $contextNode);

        foreach ($ingredientNodes as $node) {
            $title = $this->extractSingleValue($xpath, "td[1]", $node);
            $unit = $this->extractSingleValue($xpath, "td[2]", $node);

            if ($title) {
                $ingredients[] = [
                    'title' => mb_strtolower(trim($title)),
                    'unit' => $unit ? trim($unit) : '',
                ];
            }
        }

        return $ingredients;
    }

    private function extractSteps(DOMXPath $xpath, \DOMElement $contextNode): array
    {
        $stepNodes = $xpath->query(".//p", $contextNode);
        $steps = [];

        foreach ($stepNodes as $node) {
            $stepText = trim($node->nodeValue);
            if ($stepText) {
                $steps[] = $stepText;
            }
        }

        return $steps;
    }

    private function extractImage(DOMXPath $xpath, \DOMElement $contextNode): ?string
    {
        $node = $xpath->query(".//img[@itemprop='image']", $contextNode)->item(0);
        return $node ? 'https://rud.ua' . $node->getAttribute('src') : null;
    }

    private function getOrCreateCategory(?string $categoryName): Category
    {
        $mappedCategories = [
            'Другі страви' => '🥘 другі страви',
            'Салати та закуски' => '🥗 салати',
            'Випічка' => '🥧 випічка',
            'Торти' => '🍰 торти',
            'Десерти' => '🍨 солодкі страви та десерти',
            'Напої' => '🍸 коктейлі та напої',
        ];

        $mappedCategory = $mappedCategories[$categoryName] ?? $categoryName ?? 'Різне';

        return Category::firstOrCreate(['title' => $mappedCategory]);
    }
}
