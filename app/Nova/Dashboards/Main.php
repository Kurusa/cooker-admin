<?php

namespace App\Nova\Dashboards;

use App\Nova\Metrics\RecipesBySource;
use App\Nova\Metrics\RecipesPerDay;
use App\Nova\Metrics\TopCategories;
use App\Nova\Metrics\TopIngredients;
use App\Nova\Metrics\TopIngredientUnits;
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
