<?php

namespace App\Nova\Filters\SourceRecipeUrl;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Laravel\Nova\Filters\BooleanFilter;

class SourceRecipeUrlExcludedFilter extends BooleanFilter
{
    public $name = 'Excluded';

    public function apply(Request $request, $query, $value): Builder
    {
        if ($value['Excluded'] ?? false) {
            $query->isExcluded();
        }

        if ($value['Not excluded'] ?? false) {
            $query->notExcluded();
        }

        return $query;
    }

    public function options(Request $request): array
    {
        return [
            'Excluded' => 'Excluded',
            'Not excluded' => 'Not excluded',
        ];
    }
}
