<?php

namespace Tests\Unit\Services\Formatters;

use App\Services\Parsers\Formatters\IngredientFormatter;
use PHPUnit\Framework\TestCase;

class IngredientFormatterTest extends TestCase
{
    public function testFormatIngredientWithQuantityAndUnit()
    {
        $result = IngredientFormatter::formatIngredient('200 г борошна');
        $this->assertEquals([
            'title' => 'борошна',
            'quantity' => '200',
            'unit' => 'г',
        ], $result);

        $result = IngredientFormatter::formatIngredient('200g борошна');
        $this->assertEquals([
            'title' => 'борошна',
            'quantity' => '200',
            'unit' => 'г',
        ], $result);

        $result = IngredientFormatter::formatIngredient('200ml - води');
        $this->assertEquals([
            'title' => 'води',
            'quantity' => '200',
            'unit' => 'мл',
        ], $result);

        $result = IngredientFormatter::formatIngredient('молоко: 2l');
        $this->assertEquals([
            'title' => 'молоко',
            'quantity' => '2',
            'unit' => 'л',
        ], $result);

        $result = IngredientFormatter::formatIngredient('картопля - 2 kg');
        $this->assertEquals([
            'title' => 'картопля',
            'quantity' => '2',
            'unit' => 'кг',
        ], $result);

        $result = IngredientFormatter::formatIngredient('масло - 2 ст.л.');
        $this->assertEquals([
            'title' => 'масло',
            'quantity' => '2',
            'unit' => 'ст.л',
        ], $result);

        $result = IngredientFormatter::formatIngredient('борошно: 1 склянка');
        $this->assertEquals([
            'title' => 'борошно',
            'quantity' => 1,
            'unit' => 'склянка',
        ], $result);
    }
}
