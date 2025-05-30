<?php

namespace App\Nova\Filters;

use App\Models\Source\Source;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class SourceFilter extends Filter
{
    public $name = 'Source';

    public function apply(Request $request, $query, $value): Builder
    {
        if ($request->resource === 'recipes') {
            return $query->whereHas('sourceRecipeUrl', function ($query) use ($value) {
                $query->where('source_id', $value);
            });
        }

        if ($request->resource === 'source-recipe-urls') {
            return $query->where('source_id', $value);
        }

        return $query;
    }

    public function options(Request $request): array
    {
        return Source::withCount('recipes')
            ->orderBy('title')
            ->get()
            ->mapWithKeys(function ($source) {
                return [$source->title . ' (' . $source->recipes_count . ')' => $source->id];
            })
            ->toArray();
    }
}
