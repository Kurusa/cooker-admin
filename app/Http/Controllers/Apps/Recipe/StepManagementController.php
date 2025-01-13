<?php

namespace App\Http\Controllers\Apps\Recipe;

use App\DataTables\Recipe\StepsDataTable;
use App\Http\Controllers\Controller;

class StepManagementController extends Controller
{
    public function index(StepsDataTable $dataTable)
    {
        return $dataTable->render('pages/apps.recipe.steps.list');
    }
}
