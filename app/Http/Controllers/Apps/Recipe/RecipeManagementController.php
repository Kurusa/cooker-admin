<?php

namespace App\Http\Controllers\Apps\Recipe;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class RecipeManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Recipe::with(['steps', 'source']);

        if ($request->has('search') && !empty($request->search)) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->has('source') && !empty($request->source)) {
            $query->whereHas('source', function ($q) use ($request) {
                $q->where('title', $request->source);
            });
        }

        $query->orderBy('created_at', 'desc');
        $recipes = $query->paginate(50);

        return view('pages/apps.recipe.recipes.list', compact('recipes'));
    }

    public function reparseRecipe(Recipe $recipe)
    {
        Artisan::call('parse:recipes', [
            'source' => $recipe->source->title,
            'recipeId' => $recipe->id,
        ]);

        return response()->json(['success' => true]);
    }

    public function reparseByIds(Request $request)
    {
        $request->validate([
            'recipe_ids' => 'required|array|min:1',
            'recipe_ids.*' => 'integer|exists:recipes,id',
        ]);

        foreach ($request->get('recipe_ids') as $id) {
            Artisan::call('parse:recipes', [
                'recipeId' => $id,
            ]);
        }

        return response()->json(['success' => true]);
    }
}
