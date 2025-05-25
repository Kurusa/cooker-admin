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
use Illuminate\Support\Facades\Log;

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
                            'content' => "На цій сторінці можуть бути один або кілька рецептів.Розпізнай кожен із них і поверни масив об’єктів у форматі JSON без жодних описів і пояснень.Кожен об’єкт має ключі:
-title(string):назва рецепта
-categories(array<string>):категорії без дублювання(це обов'язкове поле,тож визнач сам,якщо не вказано)
-complexity(string):easy|medium|hard(визнач сам,якщо не вказано)
-cookingTime(int):загальний час у хвилинах
-portions(int):кількість порцій(визнач сам,якщо не вказано)
-image(string):URL головного зображення рецепта
-ingredients(array<object>):кожен об’єкт має title,unit,quantity(завжди float або int).Якщо трапляються кілька записів з однаковою парою title+unit,підсумуй їхні quantity і поверни єдиний об’єкт
-cuisines(array<string>):кухня страви(визнач сам,якщо не вказано)
-steps(array<object>):кожен об’єкт має description,image
Обов’язкові правила:
Уніфікуй одиниці виміру лише за написанням,НЕ конвертуючи значення(г,кг,мл,л,ч.л,ст.л,склянка,чашка тощо;cups не перетворюй у л,а у склянки).
'За бажанням'-пропускай,це не юніт.
Завжди перекладай українською,у називному відмінку,нижній регістр,без дублікатів.
Якщо кількість є,а одиниці немає і це штуковий інгредієнт(яйця,огірки тощо),став unit=шт
Для спецій типу сіль,перець тощо,якщо кількість та одиниця не вказані — не додавай quantity і unit
Прибери будь-які префікси виду крок N або їхні варіації на початку описів кроків
Ігноруй неінформативні кроки,що містять лише слова на кшталт “Подаємо”,“Смачного”,“Enjoy”
Якщо на сторінці один рецепт поверни масив із одним об’єктом. Ось HTML:" . $html,
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

        try {
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
        } catch (Exception $exception) {
            Log::error('Exception when builder response from Deepseek. Response:' . json_encode($recipes) . '. Error:' . $exception->getMessage());

            throw $exception;
        }
    }
}
