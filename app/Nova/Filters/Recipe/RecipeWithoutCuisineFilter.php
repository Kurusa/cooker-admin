<?php

namespace App\Nova\Filters\Recipe;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Laravel\Nova\Filters\BooleanFilter;

class RecipeWithoutCuisineFilter extends BooleanFilter
{
    public $name = 'Without cuisine';

    public function apply(Request $request, $query, $value): Builder
    {
        if ($value['Show'] === true) {
            return $query->whereDoesntHave('cuisines');
        }

        return $query;
    }

    public function options(Request $request): array
    {
        return [
            'Show' => 'Show',
        ];
    }
}
