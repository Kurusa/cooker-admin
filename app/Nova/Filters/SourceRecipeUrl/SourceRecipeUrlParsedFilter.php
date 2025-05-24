<?php

namespace App\Nova\Filters\SourceRecipeUrl;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Laravel\Nova\Filters\BooleanFilter;

class SourceRecipeUrlParsedFilter extends BooleanFilter
{
    public $name = 'Parsed';

    public function apply(Request $request, $query, $value): Builder
    {
        if ($value['Parsed'] ?? false) {
            $query->isParsed();
        }

        if ($value['Not parsed'] ?? false) {
            $query->notParsed();
        }

        return $query;
    }

    public function options(Request $request): array
    {
        return [
            'Parsed' => 'Parsed',
            'Not parsed' => 'Not parsed',
        ];
    }
}
