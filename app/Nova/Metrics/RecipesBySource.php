<?php

namespace App\Nova\Metrics;

use App\Models\Source\Source;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\MetricTableRow;
use Laravel\Nova\Metrics\Table;

class RecipesBySource extends Table
{
    public function name(): string
    {
        return 'Recipes by source';
    }

    public function calculate(NovaRequest $request): array
    {
        return Source::withCount([
            'recipes',
            'recipeUrls as not_excluded_urls_count' => fn($q) => $q->notExcluded()
        ])
            ->orderByDesc('recipes_count')
            ->get()
            ->map(function (Source $source) {
                return MetricTableRow::make()
                    ->title($source->title)
                    ->subtitle($source->getParsedSummaryText())
                    ->icon('book-open')
                    ->iconClass('text-blue-500');
            })->all();
    }

    public function uriKey(): string
    {
        return 'recipes-by-source-table';
    }
}
