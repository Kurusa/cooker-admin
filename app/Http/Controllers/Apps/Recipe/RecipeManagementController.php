<?php

namespace App\Http\Controllers\Apps\Recipe;

use App\DataTables\Recipe\RecipesDataTable;
use App\Http\Controllers\Controller;

class RecipeManagementController extends Controller
{
    public function index(RecipesDataTable $dataTable)
    {
        return $dataTable->render('pages/apps.recipe.recipes.list');
    }
}
