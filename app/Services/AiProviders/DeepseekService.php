<?php

namespace App\Services\AiProviders;

use App\DTO\IngredientDTO;
use App\DTO\IngredientGroupDTO;
use App\DTO\RecipeCategoryDTO;
use App\DTO\RecipeCuisineDTO;
use App\DTO\RecipeDTO;
use App\DTO\StepDTO;
use App\Enums\Recipe\ComplexityEnum;
use App\Exceptions\AiProviderDidntFindRecipeException;
use App\Models\Recipe\RecipeCuisine;
use App\Services\AiProviders\Contracts\AiRecipeParserServiceInterface;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class DeepseekService implements AiRecipeParserServiceInterface
{
    public function __construct(private readonly Client $client)
    {
    }

    public function parse(string $html): array
    {
        $categories = DB::table('recipe_categories')
            ->whereIn('id', function ($query) {
                $query->select('category_id')
                    ->from('recipe_category_parent_map');
            })
            ->pluck('title')
            ->toArray();

        $cuisines = RecipeCuisine::all()
            ->pluck('title')
            ->toArray();

        $response = $this->client->post('/chat/completions', [
            'json' => [
                'model' => 'deepseek-chat',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a helpful assistant.'],
                    [
                        'role' => 'user',
                        'content' => "На цій сторінці можуть бути один або кілька рецептів.Розпізнай кожен із них і поверни масив об'єктів у форматі
JSON без жодних описів і пояснень.Кожен об'єкт має ключі:
title(string)
якщо не вказано.Використовуй загальновідомі категорії рецептів,відкидай неінформативні категорії,наприклад інгредієнти чи просто теги.
відкидай неінформативні категорії,типу інгредієнтів чи просто тегів
complexity(string):easy|medium|hard.визнач сам,якщо не вказано
cookingTime(int):загальний час у хвилинах
portions(int):кількість порцій.визнач сам,якщо не вказано
image(string):URL головного зображення рецепта
ingredientGroups(array<object>):об'єкт з ключами
-group(string):e.x,'для тіста'
-ingredients(array<object>):title(видаляй лишні символи типу дужок),unit,quantity(завжди int або float,це обовязково.не може бути стрінгою.
дроби переводь у float).if груп немає–поверни 1 елемент зі group =''та всі інгредієнти всередині
-cuisines(array<string>):кухня,яка найкраще описує рецепт.Вибирай ТІЛЬКИ з цього списку:
[\"" . implode('","', $cuisines) . "\"].
-categories(array<string>):масив категорій, які найкраще описують рецепт.Вибирай ТІЛЬКИ з цього списку:
[\"" . implode('","', $categories) . "\"].
-steps(array<object>):кожен об'єкт з description,image
Обов'язкові правила:якщо є юніт 'півчогось(склянки)'-перетворюй у 'склянка'.приводь до називного відмінку,або скороченого варіанту-'ст. ложки'-'ст. л.'.
якщо к-ть=0,невказуй unit.якщо є unit і к-сть,але немає назви інгредієнту-це помилка.'За бажанням','для тіста','для прикраси'тощо-це не юніт,
вказуй порожній.Завжди все українською,у називному відмінку,нижній регістр,без дублікатів.русняве/суржик-переводь українською.
Якщо к-сть є,а одиниці немає і це штуковий інгредієнт(яйця,огірки тощо),став unit=шт.Для спецій типу сіль,перець тощо,якщо к-сть і unit не вказані—
не додавай quantity і unit.Прибери будь-які префікси виду крок N чи варіації на початку описів кроків.Ігноруй неінформативні кроки,що містять лише
слова типу'Подаємо','Смачного','Enjoy'.Якщо на сторінці один рецепт поверни масив із одним об'єктом.Якщо рецепта немає кроків чи інгредієнтів,
поверни порожню відповідь.HTML:" . $html,
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
                    complexity: ComplexityEnum::from($response['complexity']),
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
        } catch (Throwable $exception) {
            Log::error('Exception when building response from Deepseek', [
                'response' => $response,
                'recipes' => $recipes,
                'error' => $exception->getMessage(),
            ]);

            return [];
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
}
