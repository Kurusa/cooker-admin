<?php

namespace App\Nova\Filters;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Laravel\Nova\Filters\BooleanFilter;

class OnlyRootCategories extends BooleanFilter
{
    public $name = 'Root Categories';

    public function apply(Request $request, $query, $value): Builder
    {
        if ($value['Only root'] === true) {
            $query->whereDoesntHave('parents');
        }

        return $query;
    }

    public function options(Request $request): array
    {
        return [
            'Only root' => 'Only root',
        ];
    }
}
