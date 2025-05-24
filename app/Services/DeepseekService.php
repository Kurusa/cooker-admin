<?php

namespace App\Services;

use App\DTO\CategoryDTO;
use App\DTO\CuisineDTO;
use App\DTO\IngredientDTO;
use App\DTO\RecipeDTO;
use App\DTO\StepDTO;
use App\Enums\Recipe\Complexity;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class DeepseekService
{
    public function __construct(private readonly Client $client)
    {
    }

    public function parseRecipeFromHtml(string $html): array
    {
        try {
            $response = $this->client->post('/chat/completions', [
                'json' => [
                    'model' => 'deepseek-chat',
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a helpful assistant.'],
                        [
                            'role' => 'user',
                            'content' => "На цій сторінці можуть бути один або кілька рецептів.Розпізнай кожен з них s поверни масив об'єктів у форматі JSON.Кожен об'єкт має такі ключі:
-title(string):назва рецепта
-categories(array of string):категорії,без дублювання
-complexity(string):easy/medium/hard,вкажи сам якщо не вказано
-cookingTime(int):загальний час у хвилинах
-portions(int):кількість порцій,вкажи сам якщо не вказано
-image(string):посилання на головне зображення рецепта
-ingredients(array of object):кожен об'єкт має ключі title,unit,quantity,originalTitle
-cuisines(array of string):кухня до якої відноситься страва,вкажи сам якщо не вказано
-steps(array of object):кожен об'єкт має ключі description,image
Поверни лише цей масив без описів і пояснень.Усі назви—українською.Уникай дублікатів інгредієнтів.
Уніфікуй юніти(грами-г,ст ложки-ст.л)українською.Назви інгредієнтів—у називному відмінку і нижньому регістрі.
Якщо на сторінці один рецепт—поверни масив з одним об'єктом.Ось HTML:" . $html,
                        ],
                    ],
                    'stream' => false,
                ],
            ]);
            return $this->parseDeepseekResponse($response->getBody()->getContents());
        } catch (RequestException $e) {
            return [];
        }
    }

    /**
     * @param string $response
     * @return array
     * @throws Exception
     */
    public function parseDeepseekResponse(string $response): array
    {
        $data = json_decode($response, true);

        if (!isset($data['choices'][0]['message']['content'])) {
            throw new Exception('Invalid response structure');
        }

        $content = $data['choices'][0]['message']['content'];

        if (preg_match('/```json\s*(\[\s*{.*?}\s*])\s*```/s', $content, $matches)) {
            $recipes = json_decode($matches[1], true);
        } else {
            throw new Exception('No recipe data found');
        }

        return array_map(function (array $response) {
            return new RecipeDTO(
                title: $response['title'],
                complexity: Complexity::from($response['complexity']),
                time: $response['cookingTime'] ?? null,
                portions: $response['portions'] ?? 1,
                imageUrl: $response['image'] ?? '',
                source_recipe_url_id: null,
                cuisines: array_map(fn($cuisine) => new CuisineDTO(title: $cuisine), $response['cuisines'] ?? []),
                categories: array_map(fn($category) => new CategoryDTO(title: $category), $response['categories'] ?? []),
                ingredients: array_map(fn($ingredient) => new IngredientDTO(
                    title: $ingredient['title'],
                    quantity: $ingredient['quantity'] ?? null,
                    unit: $ingredient['unit'] ?? null,
                    originalTitle: $ingredient['originalTitle'] ?? null,
                ), $response['ingredients'] ?? []),
                steps: array_map(fn($step) => new StepDTO(
                    description: $step['description'],
                    image: $step['image'] ?? '',
                ), $response['steps'] ?? []),
            );
        }, $recipes);
    }
}
