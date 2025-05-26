<?php

namespace App\Nova\Metrics;

use App\Models\Ingredient\Ingredient;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\MetricTableRow;
use Laravel\Nova\Metrics\Table;

class TopIngredients extends Table
{
    public function name(): string
    {
        return 'Top 10 ingredients';
    }

    public function calculate(NovaRequest $request): array
    {
        $results = DB::table('recipe_ingredients')
            ->join('ingredient_units', 'recipe_ingredients.ingredient_unit_id', '=', 'ingredient_units.id')
            ->select('ingredient_units.ingredient_id', DB::raw('count(*) as used_count'))
            ->groupBy('ingredient_units.ingredient_id')
            ->orderByDesc('used_count')
            ->limit(10)
            ->get();

        $ingredients = Ingredient::whereIn('id', $results->pluck('ingredient_id'))->get()->keyBy('id');

        return $results->map(function ($row) use ($ingredients) {
            return MetricTableRow::make()
                ->icon('cube')
                ->iconClass('text-emerald-500')
                ->title($ingredients[$row->ingredient_id]->title ?? 'Інгредієнт?')
                ->subtitle("Використано у {$row->used_count} рецептах");
        })->all();
    }
}
