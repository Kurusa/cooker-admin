<?php

namespace App\Nova\Dashboards;

use App\Nova\Metrics\RecipesBySource;
use Laravel\Nova\Dashboards\Main as Dashboard;

class Main extends Dashboard
{
    public function cards(): array
    {
        return [
            new RecipesBySource,
        ];
    }
}
