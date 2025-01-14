<?php

namespace App\Console\Commands\Parsers;

use App\Enums\Recipe\Complexity;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\Step;
use DOMDocument;
use DOMXPath;
use Exception;
use Illuminate\Console\Command;

class PatelnyaParser extends Command
{
    protected $signature = 'parse:patelnya';

    private array $categoryMapping = [
        'Дешево та швидко' => '🍳 сніданки',
        'Десерти' => '🍨 солодкі страви та десерти',
        'Гарніри' => '🥘 другі страви',
        'Великдень' => 'Великодні страви',
        'Закуски' => 'закуски',
        'Консервація' => 'варення',
        'Весняні салати' => '🥗 салати',
        'Здорове харчування - Рецепти' => 'дієтичні страви',
        '8 березня' => 'Святкові страви',
        'Дієти' => 'дієтичні страви',
        'День Святого Валентина' => 'Святкові страви',
        'Здорове харчування' => 'дієтичні страви',
        'Готуємо з розумною технікою' => 'страви в мультиварці',
        'Другі страви' => '🥘 другі страви',
    ];

    public function handle(): void
    {
        $sitemapUrl = 'https://patelnya.com.ua/post-sitemap.xml';
        $sitemapElements = simplexml_load_file($sitemapUrl);

        foreach ($sitemapElements as $sitemapElement) {
            $url = (string)$sitemapElement->loc;

            if (Recipe::where('source_url', $url)->exists()) {
                continue;
            }

            $xpath = $this->loadHtml(file_get_contents($url));

            $data = $this->extractRecipeData($xpath);
            dd($data);
            if (!$this->isValidRecipe($data)) {
                $this->info('No steps or ingredients found for recipe: ' . $url);
                continue;
            }

            try {
                $category = $this->getOrCreateCategory($data['category']);
                $recipe = $this->createRecipe($data, $category, $url);

                $this->attachStepsToRecipe($data['steps'], $recipe);
                $this->attachIngredientsToRecipe($data['ingredients'], $recipe);

                $this->info('Recipe created: ' . $recipe->title);
            } catch (Exception $e) {
                continue;
            }
        }
    }

    private function loadHtml($html): DOMXPath|string
    {
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        $dom->loadHTML($html);
        return new DOMXPath($dom);
    }

    private function extractRecipeData(DOMXPath $xpath): array
    {
        return [
            'title' => $this->extractSingleValue($xpath, ".//h1[@class='p-name name-title fn']"),
            'complexity' => Complexity::mapParsedValue(mb_strtolower($this->extractSingleValue($xpath, ".//div[i/span[contains(text(), 'Рівень складності:')]]/i/span[@class='color-414141']"))),
            'portions' => $this->extractSingleValue($xpath, ".//div[i/span[contains(text(), 'Кількість порцій:')]]/i/span[@class='color-414141 yield']"),
            'cooking_time' => $this->extractSingleValue($xpath, ".//div[i[@class='duration']]/i/span[@class='color-414141 value-title']"),
            'ingredients' => $this->extractIngredients($xpath),
            'steps' => $this->extractSteps($xpath),
            'image' => $this->extractImage($xpath),
            'category' => $this->extractCategory($xpath),
        ];
    }

    private function isValidRecipe(array $data): bool
    {
        return count($data['steps']) > 0 && count($data['ingredients']) > 0;
    }

    private function createRecipe(array $data, Category $category, string $sourceUrl): Recipe
    {
        return Recipe::create([
            'title' => $data['title'],
            'complexity' => $data['complexity'],
            'portions' => $data['portions'],
            'time' => $data['cooking_time'],
            'image_url' => $data['image'],
            'source_url' => $sourceUrl,
            'category_id' => $category->id,
        ]);
    }

    private function attachStepsToRecipe(array $steps, Recipe $recipe): void
    {
        $result = [];
        foreach ($steps as $step) {
            $result[] = new Step([
                'description' => $step,
                'recipe_id' => $recipe->id,
            ]);
        }
        $recipe->steps()->saveMany($result);
    }

    private function attachIngredientsToRecipe(array $ingredients, Recipe $recipe): void
    {
        foreach ($ingredients as $ingredient) {
            $ingredientDb = Ingredient::firstOrCreate([
                'title' => $ingredient['title'],
                'unit' => $ingredient['unit'],
            ]);
            $recipe->ingredients()->attach($ingredientDb->id);
        }
    }

    private function extractSingleValue(DOMXPath $xpath, string $query): ?string
    {
        $node = $xpath->query($query)->item(0);
        return $node ? str_replace('min', 'хвилин', trim($node->nodeValue)) : null;
    }

    private function extractIngredients(DOMXPath $xpath): array
    {
        $ingredients = [];
        $ingredientNodesOld = $xpath->query(".//div[@class='list-ingredient old-list']//ul[@class='ingredient']/li");

        foreach ($ingredientNodesOld as $node) {
            $name = trim($node->childNodes->item(0)->nodeValue ?? '');
            $unitNode = $node->getElementsByTagName('span')->item(0);
            $unit = $unitNode ? trim($unitNode->nodeValue, ",. ") : '';
            $title = str_replace([' -', ' :', ':'], '', mb_strtolower($name));

            if ($title === 'для начинки' || $title === 'для тіста') {
                continue;
            }

            $ingredients[] = [
                'title' => $title,
                'unit' => $unit,
            ];
        }

        if (count($ingredients)) {
            return $ingredients;
        }

        $ingredientNodesNew = $xpath->query(".//div[@class='list-ingredient old-list']//ul/li");

        foreach ($ingredientNodesNew as $node) {
            $text = trim($node->nodeValue);
            $text = strip_tags($text);
            $text = preg_replace('/\s+/', ' ', $text);
            $text = rtrim($text, ',.');

            $parts = explode('-', $text, 2);
            $name = str_replace([' -', ' :', ':'], '', mb_strtolower(trim($parts[0] ?? '')));
            $unit = trim($parts[1] ?? '');

            if ($name === 'для начинки' || $name === 'для тіста') {
                continue;
            }

            $ingredients[] = [
                'title' => $name,
                'unit' => $unit,
            ];
        }

        return $ingredients;
    }

    private function extractSteps(DOMXPath $xpath): array
    {
        $steps = [];
        $listNodes = $xpath->query(".//div[@class='e-instructions step-instructions instructions']//ol/li");

        foreach ($listNodes as $node) {
            $text = preg_replace('/<a[^>]*>(.*?)<\/a>/', '', trim($node->nodeValue ?? ''));
            if (!empty($text)) {
                $steps[] = $text;
            }
        }

        $paragraphNodes = $xpath->query(".//div[@class='e-instructions step-instructions instructions']/p");

        foreach ($paragraphNodes as $node) {
            $text = preg_replace('/<a[^>]*>(.*?)<\/a>/', '', trim($node->nodeValue ?? ''));

            if (preg_match('/^\\d+\\.\\s*/', $text)) {
                $text = preg_replace('/^\\d+\\.\\s*/', '', $text);
                $steps[] = $text;
            } elseif (!empty($steps)) {
                $steps[count($steps) - 1] .= "\n" . $text;
            }
        }

        return $steps;
    }

    private function extractImage(DOMXPath $xpath): ?string
    {
        $imageNode = $xpath->query(".//img[contains(@class, 'article-img-left')]")->item(0);
        return $imageNode ? trim($imageNode->getAttribute('src')) : null;
    }

    private function extractCategory(DOMXPath $xpath): ?string
    {
        $categoryNode = $xpath->query(".//div[@class='title-detail']/a/span")->item(0);
        return $categoryNode ? trim($categoryNode->nodeValue) : null;
    }

    private function getOrCreateCategory(?string $categoryName): Category
    {
        if (!$categoryName) {
            return Category::firstOrCreate(['title' => 'новинки']);
        }

        $mappedCategory = $this->categoryMapping[$categoryName] ?? $categoryName;
        return Category::firstOrCreate(['title' => $mappedCategory]);
    }
}
