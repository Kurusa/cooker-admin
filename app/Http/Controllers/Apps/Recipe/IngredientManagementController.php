<?php

namespace App\Http\Controllers\Apps\Recipe;

use App\DataTables\Recipe\IngredientsDataTable;
use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function getDetails(int $ingredientId)
    {
        $ingredientUnits = DB::table('ingredient_units')
            ->join('units', 'ingredient_units.unit_id', '=', 'units.id')
            ->join('recipe_ingredients', 'ingredient_units.id', '=', 'recipe_ingredients.ingredient_unit_id')
            ->select(
                'ingredient_units.id as ingredient_unit_id',
                'units.title as unit_title',
                'recipe_ingredients.quantity as quantity',
                DB::raw('COUNT(recipe_ingredients.id) as recipe_count')
            )
            ->where('ingredient_units.ingredient_id', $ingredientId)
            ->groupBy('ingredient_units.id', 'units.title', 'recipe_ingredients.quantity')
            ->orderBy('units.title')
            ->orderBy('recipe_ingredients.quantity')
            ->get();

        $html = view('livewire.recipe.view-ingredient-details', compact('ingredientUnits'))->render();

        return response()->json(['html' => $html]);
    }
}
