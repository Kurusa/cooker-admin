<?php

namespace App\Nova\Metrics;

use App\Models\Recipe\Recipe;
use App\Models\Source\SourceRecipeUrl;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;
use Laravel\Nova\Metrics\PartitionResult;

class RecipesBySource extends Partition
{
    public function name(): string
    {
        return 'Рецепти за джерелами';
    }

    public function calculate(NovaRequest $request): PartitionResult
    {
        return $this->count($request, Recipe::class, 'source_recipe_url_id')
            ->label(function ($value) {
                return SourceRecipeUrl::with('source')
                    ->find($value)?->source->title;
            });
    }

    public function uriKey(): string
    {
        return 'recipes-by-source';
    }
}
