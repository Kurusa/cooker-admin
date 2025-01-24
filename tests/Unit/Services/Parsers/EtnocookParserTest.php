<?php

namespace Tests\Unit\Services\Parsers;

use App\Enums\Recipe\Complexity;
use App\Models\Source;
use App\Services\Parsers\Contracts\RecipeParserInterface;
use App\Services\Parsers\RecipeParserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EtnocookParserTest extends TestCase
{
    use RefreshDatabase;

    private RecipeParserInterface $parser;

    public function setUp(): void
    {
        parent::setUp();

        Source::create([
            'title' => 'etnocook',
            'url' => '',
        ]);

        $factory = app(RecipeParserFactory::class);
        $this->parser = $factory->make('etnocook');

        $html = file_get_contents(base_path('tests/Unit/Services/fixtures/parsers/etnocook/sample_recipe.html'));

        $this->parser->loadHTML(null, $html);
    }

    public function testParsesTitleCorrectly(): void
    {
        $title = $this->parser->parseTitle();

        $this->assertEquals('суп з кропивою', $title);
    }

    public function testParsesCategoriesCorrectly(): void
    {
        $categories = $this->parser->parseCategories();

        $this->assertCount(2, $categories);
        $this->assertEquals([
            'перші страви',
            'юшки',
        ], $categories);
    }

    public function testParsesComplexityCorrectly()
    {
        $complexity = $this->parser->parseComplexity();

        $this->assertEquals(Complexity::EASY, $complexity);
    }

    public function testParsesCookingTimeCorrectly()
    {
        $cookingTime = $this->parser->parseCookingTime();

        $this->assertEquals(60, $cookingTime);
    }

    public function testParsesPortionsCorrectly()
    {
        $cookingTime = $this->parser->parsePortions();

        $this->assertEquals(1, $cookingTime);
    }

    public function testParsesIngredientsCorrectly()
    {
        $ingredients = $this->parser->parseIngredients(true);

        $this->assertEquals([
            "листя кропиви…….30г. бульйон (овочевий або м'ясний)…….2 л капуста…………….150 гр. зелень кропу…………….10 гр. зелень петрушки…………5 гр. зелена цибуля…………5 гр. часник…………….5 гр",
            "за бажанням  морква…………..50 гр.  сіль……………за смаком (~5-6 гр.)  цукор…………..за смаком (~2-3 гр.)  перць чорний мелений……за смаком  яйце куряче……2 шт"
        ], $ingredients);
    }


}
