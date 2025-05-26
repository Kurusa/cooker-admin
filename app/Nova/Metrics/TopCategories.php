<?php

namespace App\Nova\Metrics;

use App\Models\Recipe\RecipeCategory;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\MetricTableRow;
use Laravel\Nova\Metrics\Table;

class TopCategories extends Table
{
    public function name(): string
    {
        return 'Top 10 categories';
    }

    public function calculate(NovaRequest $request): array
    {
        $results = DB::table('recipe_categories_map')
            ->select('category_id', DB::raw('count(*) as recipes_count'))
            ->groupBy('category_id')
            ->orderByDesc('recipes_count')
            ->limit(10)
            ->get();

        $categories = RecipeCategory::whereIn('id', $results->pluck('category_id'))->get()->keyBy('id');

        return $results->map(function ($row) use ($categories) {
            return MetricTableRow::make()
                ->icon('tag')
                ->title($categories[$row->category_id]->title ?? 'Категорія?')
                ->subtitle("Рецептів: {$row->recipes_count}");
        })->all();
    }

    public function uriKey(): string
    {
        return 'top-categories';
    }
}
