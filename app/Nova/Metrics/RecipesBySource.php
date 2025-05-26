<?php

namespace App\Nova\Metrics;

use Illuminate\Support\Facades\DB;
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
        $data = DB::table('recipes')
            ->join('source_recipe_urls', 'recipes.source_recipe_url_id', '=', 'source_recipe_urls.id')
            ->join('sources', 'source_recipe_urls.source_id', '=', 'sources.id')
            ->select('sources.title as label', DB::raw('COUNT(recipes.id) as value'))
            ->groupBy('sources.title')
            ->pluck('value', 'label');

        return $this->result($data->toArray());
    }

    public function uriKey(): string
    {
        return 'recipes-by-source';
    }
}
