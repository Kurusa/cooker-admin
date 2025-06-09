<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;

class DebugController extends BaseController
{
    public function debug()
    {
        $categories = DB::table('recipe_categories')
            ->whereIn('id', function ($query) {
                $query->select('category_id')
                    ->from('recipe_category_parent_map');
            })
            ->pluck('title')
            ->toArray();

        dd($categories);
    }
}
