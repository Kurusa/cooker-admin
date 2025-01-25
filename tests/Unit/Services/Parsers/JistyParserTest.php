<?php

namespace Tests\Unit\Services\Parsers;

use App\DTO\CategoryDTO;
use App\DTO\IngredientDTO;
use App\DTO\RecipeDTO;
use App\DTO\StepDTO;
use App\Enums\Recipe\Complexity;
use App\Services\Parsers\Contracts\RecipeParserInterface;
use App\Services\Parsers\RecipeParserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JistyParserTest extends TestCase
{
    use RefreshDatabase;

    private RecipeParserInterface $parser;

    public function setUp(): void
    {
        parent::setUp();

        $factory = app(RecipeParserFactory::class);
        $this->parser = $factory->make('jisty');

        $html = file_get_contents(base_path('tests/Unit/Services/fixtures/parsers/jisty/sample_recipe.html'));

        $this->parser->loadHTML(null, $html);
    }

    public function testParseRecipe()
    {
        $recipes = $this->parser->parseRecipes(true);

        $this->assertCount(1, $recipes);
        $this->assertInstanceOf(RecipeDTO::class, $recipes[0]);
        $this->assertEquals(new RecipeDTO(
            title      : 'рецепт картоплі з сиром на гарнір',
            complexity : Complexity::MEDIUM,
            time       : null,
            portions   : 1,
            imageUrl   : 'https://jisty.com.ua/wp-content/uploads/2019/11/kartoplya-z-sirom-u-folzi.jpg',
            categories : [new CategoryDTO(title: 'гарячі страви')],
            ingredients: [
                new IngredientDTO(
                    title: '2 столові ложки майонезу',
                ),
                new IngredientDTO(
                    title: '50 грамів вершкового масла',
                ),
                new IngredientDTO(
                    title: '1 столова ложка зелені кропу',
                ),
            ],
            steps      : [
                new StepDTO(
                    description: "картоплю дуже добре помити, кожну картоплину нечищеною загорнути у фольгу, викласти на деко й поставити у духовку. пекти при температурі 180 градусів до готовності",
                ),
                new StepDTO(
                    description: "сир потерти на грубу тертку, додати м'яке масло, майонез, часник і кріп. ретельно розмішати і з цієї маси сформувати кульки у тій кількості, скільки печеться картоплин",
                ),
            ],
        ), $recipes[0]);
    }

    public function testParseMultipleRecipes()
    {
        $html = file_get_contents(base_path('tests/Unit/Services/fixtures/parsers/jisty/sample_multiple_recipes.html'));
        $this->parser->loadHTML(null, $html);

        $recipes = $this->parser->parseRecipes(true);
        $this->assertCount(7, $recipes);
        $this->assertEquals(new RecipeDTO(
            title      : 'бутербродний паштет з квасолі',
            complexity : Complexity::MEDIUM,
            time       : null,
            portions   : 1,
            imageUrl   : 'https://jisty.com.ua/wp-content/uploads/2020/12/pashtet-2.jpg.webp',
            categories : [new CategoryDTO(title: 'гарячі страви')],
            ingredients: [
                new IngredientDTO(
                    title: '200 г вареної квасолі',
                ),
                new IngredientDTO(
                    title: '1 солоний огірок',
                ),
            ],
            steps      : [
                new StepDTO(
                    description: 'відварену квасолю подрібнюємо в блендері',
                ),
                new StepDTO(
                    description: 'нарізаємо помідори, часник, огірок, лимонний сік, оливкову олію, спеції та збиваємо в блендері',
                ),
                new StepDTO(
                    description: 'змішуємо суміш з квасолею та подаємо намастивши на хліб',
                ),
            ],
        ), $recipes[0]);
    }
}
