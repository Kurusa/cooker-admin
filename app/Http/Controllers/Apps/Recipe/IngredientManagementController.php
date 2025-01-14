<?php

namespace App\Http\Controllers\Apps\Recipe;

use App\DataTables\Recipe\IngredientsDataTable;
use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use Illuminate\Http\Request;

class IngredientManagementController extends Controller
{
    public function index(IngredientsDataTable $dataTable)
    {
        return $dataTable->render('pages/apps.recipe.ingredients.list');
    }

    public function update(Request $request, int $ingredientId)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        /** @var Ingredient $ingredient */
        $ingredient = Ingredient::find($ingredientId);
        $ingredient->update(['title' => $validated['title']]);

        return response()->json(['success' => true, 'message' => 'Ingredient updated successfully']);
    }
}
