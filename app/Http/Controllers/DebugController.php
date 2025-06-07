<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;

class DebugController extends BaseController
{
    public function debug()
    {
        $units = Unit::withCount(['ingredientUnits as ingredient_count' => function ($query) {
            $query->select(DB::raw('count(distinct ingredient_id)'));
        }])->orderBy('title')->get();

        return view('drag-units', [
            'units' => $units,
        ]);
    }
}
