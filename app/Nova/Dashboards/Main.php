<?php

namespace App\Nova\Dashboards;

use App\Nova\Metrics\RecipesPerDay;
use Laravel\Nova\Dashboards\Main as Dashboard;

class Main extends Dashboard
{
    public function cards(): array
    {
        return [
            new RecipesPerDay,
//            new RecipesBySource,
//            new TopCategories,
//            new TopIngredients,
//            new TopIngredientUnits,
        ];
    }
}
