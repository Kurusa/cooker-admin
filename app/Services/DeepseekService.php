<?php

namespace App\Services;

use App\DTO\IngredientDTO;
use App\DTO\StepDTO;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;

class DeepseekService
{
    public function __construct(private readonly Client $client)
    {
    }

    /**
     * @param string $ingredients
     *
     * @return IngredientDTO[]
     *
     * @throws GuzzleException
     * @throws Exception
     */
    public function parseIngredients(string $ingredients): array
    {
        try {
            $response = $this->client->post('/chat/completions', [
                'json' => [
                    'model' => 'deepseek-chat',
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a helpful assistant.'],
                        [
                            'role' => 'user',
                            'content' => "Розпарси інгредієнти у форматі JSON-масиву з об'єктами,де кожен об'єкт має ключі:title(string),unit(string),quantity(int|float),
                            originalTitle(string).Поверни лише цей масив без пояснень,без додаткового тексту і описів.
                            Складні інгредієнти не розбивай на підінгредієнти.Юніти скорочуй без крапок, уніфіковуй (грами-г, ст ложки-ст.л) і надавай українською.Порожні поля залишай пустими.Назви інгредієнтів—у називному відмінку і нижньому регістрі.
                            Замінюй нетипові лапки на стандартні.У originalTitle передай оригінальну назву інгредієнта.
                            Якщо інгредієнт без юніту і його назва є накшталт 'яйце','картопля','помідор',встанови 'шт'.Інгредієнти:" . $ingredients],
                    ],
                    'stream' => false,
                ],
            ]);

            $responseData = $this->parseDeepseekResponse($response->getBody()->getContents());

            return array_map(
                fn(array $ingredientData) => new IngredientDTO(
                    title: (string)$ingredientData['title'],
                    quantity: (float)$ingredientData['quantity'] ?? null,
                    unit: (string)$ingredientData['unit'] ?? null,
                    originalTitle: $ingredientData['originalTitle'] ?? null,
                ),
                $responseData
            );
        } catch (RequestException $e) {
            return [];
        }
    }

    /**
     * @param string $steps
     *
     * @return StepDTO[]
     *
     * @throws GuzzleException
     */
    public function parseSteps(string $steps): array
    {
        try {
            $response = $this->client->post('/chat/completions', [
                'json' => [
                    'model' => 'deepseek-chat',
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a helpful assistant.'],
                        [
                            'role' => 'user',
                            'content' => "Розпарси кроки у форматі JSON-масиву з об'єктами,де кожен об'єкт має ключі:description,image.
                            Поверни лише цей масив без пояснень,без додаткового тексту і описів.Фільтруй будь-які кроки, які містять слова на
                            кшталт 'смачного','примітки','кулінарні поради','instagram' або інші зайві дані.
                            Якщо зображення немає,залишай image порожнім.Все у нижньому регістрі.Кроки:" . $steps],
                    ],
                    'stream' => false,
                ],
            ]);

            $responseData = $this->parseDeepseekResponse($response->getBody()->getContents());

            return array_map(
                fn(array $stepData) => new StepDTO(
                    description: (string)$stepData['description'],
                    image: (float)$stepData['image'] ?? null,
                ),
                $responseData
            );
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

        preg_match('/\[\s*{.*?}\s*\]/s', $content, $matches);

        if (empty($matches)) {
            throw new Exception('No data found in response');
        }

        return json_decode($matches[0], true);
    }
}
