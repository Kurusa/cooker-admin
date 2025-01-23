<?php

namespace App\Http\Controllers\Apps\Recipe;

use App\DataTables\Recipe\UnitsDataTable;
use App\Http\Controllers\Controller;
use App\Models\Unit;

class UnitController extends Controller
{
    public function index(UnitsDataTable $dataTable)
    {
        return $dataTable->render('pages/apps.recipe.units.list');
    }

    public function getDetails(Unit $unit)
    {
        $html = view('livewire.recipe.view-unit-details', compact('unit'))->render();

        return response()->json(['html' => $html]);
    }
}
