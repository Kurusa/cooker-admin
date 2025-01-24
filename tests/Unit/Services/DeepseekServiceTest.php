<?php

namespace Tests\Unit\Services;

use App\Services\DeepseekService;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DeepseekServiceTest extends TestCase
{
    private DeepseekService $deepseekService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->deepseekService = new DeepseekService($this->createMock(Client::class));
    }

    public function testParseDeepseekResponseReturnsCorrectIngredients()
    {
        $mockResponse = new Response(200, [], json_encode([
            'choices' => [
                [
                    'message' => [
                        'content' => 'Ось розпарсені інгредієнти у вказаному форматі:
                        ```json
                        [
                            {"title": "вино", "unit": "мл", "quantity": "200"},
                            {"title": "сіль", "unit": "", "quantity": ""},
                            {"title": "перець", "unit": "", "quantity": ""}
                        ]
                        ```'
                    ]
                ]
            ]
        ]));

        $expectedIngredients = [
            ['title' => 'вино', 'unit' => 'мл', 'quantity' => '200'],
            ['title' => 'сіль', 'unit' => '', 'quantity' => ''],
            ['title' => 'перець', 'unit' => '', 'quantity' => ''],
        ];

        $actualIngredients = $this->deepseekService->parseDeepseekResponse($mockResponse->getBody()->getContents());

        $this->assertEquals($expectedIngredients, $actualIngredients);
    }
}
