<?php

namespace App\Services\AiProviders;

use App\Exceptions\AiProviderDidntFindRecipeException;
use App\Services\AiProviders\Contracts\AiRecipeParserServiceInterface;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Collection;

class OpenAiService implements AiRecipeParserServiceInterface
{
    public function __construct(private readonly Client $client)
    {
    }

    public function parse(string $html): array
    {
        return [];
    }

    public function categorizeRecipes(Collection $recipes): array
    {
        $response = $this->client->post('responses', [
            'json' => [
                'model' => 'gpt-4.1',
                'input' => "Для кожного рецепта проаналізуй назву і інгредієнти.Признач 3–6 категорій.Кожна категорія може мати багато батьків(parent_titles).Використовуй
найвідоміші категорії рецептів.Визнач 1–3 кухні світу(cuisines),наприклад, «Італійська», «Мексиканська».Вказуй лише загальновизнані кухні.Формат
відповіді (масив):[{'id': 123,'categories':[{'title':'Риба','parent_titles':['Морепродукти']},{'title':'Морепродукти','parent_titles':
['Основні страви']}],'cuisines': ['Японська', 'Італійська']}].Вимоги до назв категорій:Однозначні, українською, однина(Супи,Випічка).
Без граматичних помилок.Рецепти:"
                    . $recipes->toJson(),
            ],
        ]);

        return $this->parseOpenAiResponse($response->getBody()->getContents());
    }

    private function parseOpenAiResponse(string $response): array
    {
        $decoded = json_decode($response, true);

        if (!isset($decoded['output'][0]['content'][0]['text'])) {
            throw new Exception('Invalid response structure');
        }

        $rawText = $decoded['output'][0]['content'][0]['text'];

        if (preg_match('/```json\s*(\[\s*{.*?}\s*])\s*```/s', $rawText, $matches)) {
            return json_decode($matches[1], true);
        }

        $trimmed = trim($rawText);
        if (str_starts_with($trimmed, '[')) {
            $parsed = json_decode($trimmed, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($parsed)) {
                return $parsed;
            }
        }

        throw new AiProviderDidntFindRecipeException(mb_strimwidth($rawText, 0, 1000, '...'));
    }
}
