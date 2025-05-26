<?php

namespace App\Nova\Metrics;

use App\Models\Recipe\Recipe;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Trend;
use Laravel\Nova\Metrics\TrendResult;

class RecipesPerDay extends Trend
{
    public function name(): string
    {
        return 'Recipes per day';
    }

    public function calculate(NovaRequest $request): TrendResult
    {
        return $this->countByDays($request, Recipe::class)
            ->showLatestValue()
            ->showSumValue();
    }

    public function ranges(): array
    {
        return [
            7 => '7 днів',
            30 => '30 днів',
            60 => '60 днів',
            90 => '90 днів',
            365 => '365 днів',
            'TODAY' => 'Сьогодні',
            'MTD' => 'Цього місяця',
            'YTD' => 'Цього року',
        ];
    }
}
