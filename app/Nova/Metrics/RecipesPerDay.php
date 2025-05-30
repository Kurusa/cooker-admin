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
        $count = match ((int) $request->range) {
            60 => $this->countByMinutes($request, Recipe::class),
            24 => $this->countByHours($request, Recipe::class),
            default => $this->countByDays($request, Recipe::class),
        };

        return $count
            ->showLatestValue()
            ->showSumValue();
    }

    public function ranges(): array
    {
        return [
            60 => 'Last hour',
            24 => 'Today',
            7 => '7 днів',
            30 => '30 днів',
        ];
    }
}
