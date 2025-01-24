<?php

namespace Tests\Unit\Services\Parsers;

use App\Enums\Recipe\Complexity;
use App\Services\Parsers\Parsers\EtnocookParser;
use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EtnocookParserTest extends TestCase
{
    use RefreshDatabase;

    private EtnocookParser $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new EtnocookParser();
    }

    public function test_parses_recipe_correctly(): void
    {
        $html = file_get_contents(base_path('tests/Unit/Services/fixtures/parsers/etnocook/sample_recipe.html'));

        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        $xpath = new DOMXPath($dom);

        $category = $this->parser->parseCategories($xpath);
        $complexity = $this->parser->parseComplexity($xpath);
        $time = $this->parser->parseCookingTime($xpath);
        $portions = $this->parser->parsePortions($xpath);
        $ingredients = $this->parser->parseIngredients($xpath, true);
        $steps = $this->parser->parseSteps($xpath);
        $image = $this->parser->parseImage($xpath);

        $this->assertEquals('суп з кропивою', $this->parser->parseTitle($xpath));
        $this->assertEquals('Очікувана категорія', $category);
//        $this->assertEquals(Complexity::MEDIUM, $complexity);
//        $this->assertEquals(30, $time);
//        $this->assertEquals(4, $portions);
//        $this->assertCount(5, $ingredients);
//        $this->assertEquals('Очікуваний інгредієнт', $ingredients[0]['title']);
//        $this->assertCount(3, $steps);
//        $this->assertEquals('Очікуваний опис кроку', $steps[0]['description']);
//        $this->assertEquals('https://expected-image-url.com', $image);
    }
}
