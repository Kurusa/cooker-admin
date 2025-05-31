<?php

namespace App\Http\Controllers;

use App\Jobs\CategorizeRecipesJob;
use App\Models\Recipe\Recipe;
use Illuminate\Routing\Controller as BaseController;

class DebugController extends BaseController
{
    public function debug(): void
    {
        $allIds = Recipe::query()
            ->doesntHave('categories')
            ->pluck('id');

        $allIds->chunk(1)->each(function ($chunkedIds) {
            CategorizeRecipesJob::dispatchSync($chunkedIds->all());
            exit();
        });
    }
}
