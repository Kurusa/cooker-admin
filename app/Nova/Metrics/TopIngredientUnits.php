<?php

namespace App\Nova\Metrics;

use App\Models\Unit;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\MetricTableRow;
use Laravel\Nova\Metrics\Table;

class TopIngredientUnits extends Table
{
    public function name(): string
    {
        return 'Top 10 units';
    }

    public function calculate(NovaRequest $request): array
    {
        $results = DB::table('recipe_ingredients_map')
            ->join('ingredient_units', 'recipe_ingredients_map.ingredient_unit_id', '=', 'ingredient_units.id')
            ->whereNotNull('ingredient_units.unit_id')
            ->select('ingredient_units.unit_id', DB::raw('count(*) as used_count'))
            ->groupBy('ingredient_units.unit_id')
            ->orderByDesc('used_count')
            ->limit(10)
            ->get();

        $units = Unit::whereIn('id', $results->pluck('unit_id'))->get()->keyBy('id');

        return $results->map(function ($row) use ($units) {
            return MetricTableRow::make()
                ->icon('hashtag')
                ->iconClass('text-orange-500')
                ->title($units[$row->unit_id]->title ?? json_encode($row))
                ->subtitle("Використано у {$row->used_count} рецептах");
        })->all();
    }
}
