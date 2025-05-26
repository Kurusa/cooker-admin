<?php

namespace App\Nova\Filters\SourceRecipeUrl;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Laravel\Nova\Filters\BooleanFilter;

class SourceRecipeUrlHasMoreThanOneRecipeFilter extends BooleanFilter
{
    public $name = 'Has >1 recipe';

    public function apply(Request $request, $query, $value): Builder
    {
        if ($value['Has >1 recipe'] ?? false) {
            $query->whereHas('recipes', function ($q) {
                $q->select('source_recipe_url_id')
                    ->groupBy('source_recipe_url_id')
                    ->havingRaw('COUNT(*) > 1');
            });
        }

        if ($value['Has 1 recipe'] ?? false) {
            $query->whereHas('recipes', function ($q) {
                $q->select('source_recipe_url_id')
                    ->groupBy('source_recipe_url_id')
                    ->havingRaw('COUNT(*) = 1');
            });
        }

        return $query;
    }

    public function options(Request $request): array
    {
        return [
            'Has >1 recipe' => 'Has >1 recipe',
            'Has 1 recipe' => 'Has 1 recipe',
        ];
    }
}
