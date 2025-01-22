<?php

namespace Tests\Unit\Services\Formatters;

use App\Services\Parsers\Formatters\CookingTimeFormatter;
use PHPUnit\Framework\TestCase;

class CookingTimeFormatterTest extends TestCase
{
    public function testFormatMinutesOnly()
    {
        $this->assertEquals(30, CookingTimeFormatter::formatCookingTime('30 хвилин'));
        $this->assertEquals(45, CookingTimeFormatter::formatCookingTime('45 хвилин'));
        $this->assertEquals(10, CookingTimeFormatter::formatCookingTime('10 хв'));
    }

    public function testFormatHoursOnly()
    {
        $this->assertEquals(60, CookingTimeFormatter::formatCookingTime('1 година'));
        $this->assertEquals(60, CookingTimeFormatter::formatCookingTime('1 год'));
        $this->assertEquals(120, CookingTimeFormatter::formatCookingTime('2 години'));
        $this->assertEquals(180, CookingTimeFormatter::formatCookingTime('3 години'));
        $this->assertEquals(180, CookingTimeFormatter::formatCookingTime('~3 години'));
        $this->assertEquals(60, CookingTimeFormatter::formatCookingTime('~ 1 год.'));
    }

    public function testFormatHoursAndMinutes()
    {
        $this->assertEquals(75, CookingTimeFormatter::formatCookingTime('1 година 15 хв'));
        $this->assertEquals(75, CookingTimeFormatter::formatCookingTime('1 год 15 хв'));
        $this->assertEquals(150, CookingTimeFormatter::formatCookingTime('2 години 30 хв'));
        $this->assertEquals(210, CookingTimeFormatter::formatCookingTime('3 години 30 хв'));
    }

    public function testFormatWithoutUnit()
    {
        $this->assertEquals(45, CookingTimeFormatter::formatCookingTime('45'));
        $this->assertEquals(60, CookingTimeFormatter::formatCookingTime('60'));
    }
}
