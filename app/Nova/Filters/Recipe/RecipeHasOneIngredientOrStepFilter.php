<?php

namespace App\Nova\Filters\Recipe;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class RecipeHasOneIngredientOrStepFilter extends Filter
{
    public $name = 'Has 1 ingredient or 1 step';

    public function apply(Request $request, $query, $value): Builder
    {
        if ($value === true) {
            return $query
                ->withCount(['recipeIngredients', 'steps'])
                ->havingRaw('recipe_ingredients_count = 1 OR steps_count = 1');
        }

        return $query;
    }

    public function options(Request $request): array
    {
        return [
            'Show' => true,
        ];
    }
}
