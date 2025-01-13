<?php

namespace App\Http\Controllers\Apps\Recipe;

use App\DataTables\Recipe\IngredientsDataTable;
use App\Http\Controllers\Controller;

class IngredientManagementController extends Controller
{
    public function index(IngredientsDataTable $dataTable)
    {
        return $dataTable->render('pages/apps.recipe.ingredients.list');
    }
}
