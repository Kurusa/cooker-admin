<?php

namespace App\Nova\Filters;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\BooleanFilter;

class HasChildrenRecipeCategories extends BooleanFilter
{
    public function name(): string
    {
        return 'Категорії з підкатегоріями';
    }

    public function apply(Request $request, $query, $value)
    {
        if ($value['Only parent'] === true) {
            return $query->whereHas('children');
        }

        return $query;
    }

    public function options(Request $request): array
    {
        return [
            'Only parent' => 'Only parent',
        ];
    }
}
