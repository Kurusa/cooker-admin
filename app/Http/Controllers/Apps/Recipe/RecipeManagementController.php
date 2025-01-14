<?php

namespace App\Http\Controllers\Apps\Recipe;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use Illuminate\Http\Request;

class RecipeManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Recipe::with('steps');

        if ($request->has('search') && !empty($request->search)) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $recipes = $query->paginate(6);

        return view('pages/apps.recipe.recipes.list', compact('recipes'));
    }
}
