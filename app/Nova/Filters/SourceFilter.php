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
        return $query->where('source_id', $value);
    }

    public function options(Request $request): array
    {
        return Source::query()
            ->orderBy('title')
            ->pluck('id', 'title')
            ->toArray();
    }
}
