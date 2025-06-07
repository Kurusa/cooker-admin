<?php

namespace App\Services\AiProviders;

use App\DTO\IngredientDTO;
use App\DTO\IngredientGroupDTO;
use App\DTO\RecipeCategoryDTO;
use App\DTO\RecipeCuisineDTO;
use App\DTO\RecipeDTO;
use App\DTO\StepDTO;
use App\Enums\Recipe\Complexity;
use App\Exceptions\AiProviderDidntFindRecipeException;
use App\Services\AiProviders\Contracts\AiRecipeParserServiceInterface;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class DeepseekService implements AiRecipeParserServiceInterface
{
    public function __construct(private readonly Client $client)
    {
    }

    public function parse(string $html): array
    {
        $response = $this->client->post('/chat/completions', [
            'json' => [
                'model' => 'deepseek-chat',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a helpful assistant.'],
                    [
                        'role' => 'user',
                        'content' => "На цій сторінці можуть бути один або кілька рецептів.Розпізнай кожен із них і поверни масив об'єктів у форматі
JSON без жодних описів і пояснень.Кожен об'єкт має ключі:title(string),cuisine(string)-кухня світу,до якої відноситься рецепт.визнач сам,якщо не вказано.
categories-масив стрінгів.також вкажи сам, якщо не вказано.відкидай неінформативні категорії,типу інгредієнтів чи просто тегів.complexity(string):easy
|medium|hard(визнач сам,якщо не вказано),cookingTime(int):загальний час у хвилинах,portions(int):кількість порцій(визнач сам,якщо не вказано),
image(string):URL головного зображення рецепта,ingredientGroups(array<object>):якщо інгредієнти розбиті по групам,кожен такий розділ–окремий об'єкт з
ключами:-group (string)–назва групи(наприклад,'для тіста'),-ingredients(array<object>):title,unit,quantity(завжди int або float,це обовязково.не може бути
стрінгою.дроби переводь у float).Якщо груп немає–поверни один елемент зі group =''та всі інгредієнти всередині.-steps(array<object>):кожен об'єкт з
description,image.Обов'язкові правила:Уніфікуй одиниці виміру лише за написанням,НЕ конвертуючи значення(г,кг,мл,л,склянка,чашка тощо;cups не перетворюй
у л).не присвоюй юніт,якщо к-сть дорівнює нулю.'За бажанням','для тіста','для прикраси'тощо-це не юніт,вказуй порожній.Завжди перекладай українською,у
називному відмінку,нижній регістр,без дублікатів.русняве/суржик-переводь українською.Якщо к-сть є,а одиниці немає і це штуковий інгредієнт(яйця,огірки
тощо),став unit=шт.Для спецій типу сіль,перець тощо,якщо к-сть і одиниця не вказані—не додавай quantity і unit.Прибери будь-які префікси виду крок N
чи їхні варіації на початку описів кроків.Ігноруй неінформативні кроки,що містять лише слова типу'Подаємо','Смачного','Enjoy'.Якщо на сторінці один
рецепт поверни масив із одним об'єктом.Якщо рецепта немає кроків чи інгредієнтів,поверни порожню відповідь.HTML:" . $html,
                    ],
                ],
                'stream' => false,
            ],
        ]);

        return $this->parseRecipeResponse($response->getBody()->getContents());
    }

    private function parseRecipeResponse(string $response): array
    {
        $recipes = $this->parseDeepseekResponse($response);

        try {
            return array_map(function (array $response) {
                return new RecipeDTO(
                    title: $response['title'],
                    complexity: Complexity::from($response['complexity']),
                    time: $response['cookingTime'] ?? null,
                    portions: $response['portions'] ?? 1,
                    imageUrl: $response['image'] ?? '',
                    source_recipe_url_id: null,
                    cuisines: array_map(fn($cuisine) => new RecipeCuisineDTO(title: $cuisine), $response['cuisines'] ?? []),
                    categories: array_map(fn($category) => new RecipeCategoryDTO(title: $category), $response['categories'] ?? []),
                    ingredientGroups: array_map(function ($group) {
                        return new IngredientGroupDTO(
                            group: $group['group'] ?? '',
                            ingredients: array_map(fn($ingredient) => new IngredientDTO(
                                title: $ingredient['title'],
                                quantity: isset($ingredient['quantity']) ? (float)$ingredient['quantity'] : null,
                                unit: $ingredient['unit'] ?? null,
                                originalTitle: $ingredient['originalTitle'] ?? null,
                            ), $group['ingredients'] ?? [])
                        );
                    }, $response['ingredientGroups'] ?? [
                        [
                            'group' => '',
                            'ingredients' => $response['ingredients'] ?? [],
                        ]
                    ]),
                    steps: array_map(fn($step) => new StepDTO(
                        description: $step['description'],
                        image: $step['image'] ?? '',
                    ), $response['steps'] ?? []),
                );
            }, $recipes);
        } catch (Exception $exception) {
            Log::error('Exception when building response from Deepseek', [
                'response' => $response,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    /**
     * @param string $response
     * @return array
     * @throws Exception
     */
    private function parseDeepseekResponse(string $response): array
    {
        $data = json_decode($response, true);

        if (!isset($data['choices'][0]['message']['content'])) {
            throw new Exception('Invalid response structure');
        }

        $response = $data['choices'][0]['message']['content'];

        if (preg_match('/```json\s*(\[\s*{.*?}\s*])\s*```/s', $response, $matches)) {
            return json_decode($matches[1], true);
        } else {
            throw new AiProviderDidntFindRecipeException(mb_strimwidth($response, 0, 1000, '...'));
        }
    }

    public function categorizeRecipes(Collection $recipes): array
    {
        // TODO: Implement categorizeRecipes() method.
    }
}
