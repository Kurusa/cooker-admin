<?php

namespace App\Services;

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
     * @param array $ingredients
     * @return array
     * @throws GuzzleException
     */
    public function parseIngredients(array $ingredients): array
    {
        try {
            $response = $this->client->post('/chat/completions', [
                'json' => [
                    'model' => 'deepseek-chat',
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a helpful assistant.'],
                        ['role' => 'user', 'content' => "Розпарси інгредієнти у форматі title,unit,quantity.Поверни кожний
                         у JSON.Юніти скорочуй без крапок, уніфіковуй (грами-г, ст ложки-ст.л) і надай українською.Порожні поля,якщо юніт чи кількість відсутні.Назви в називному
                          відмінку і нижньому регістрі.Замінюй нетипові лапки на стандартні.Інгредієнти:" . implode(',', $ingredients)],
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

        preg_match('/\[\s*{.*?}\s*\]/s', $content, $matches);

        if (empty($matches)) {
            throw new Exception('No ingredients found in response');
        }

        return json_decode($matches[0], true);
    }
}
