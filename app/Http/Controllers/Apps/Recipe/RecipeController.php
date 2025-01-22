<?php

namespace App\Http\Controllers\Apps\Recipe;

use App\Http\Controllers\Controller;
use App\Http\Requests\Recipe\DebugRecipeParseRequest;
use App\Http\Requests\Recipe\DeleteRecipeByIdsRequest;
use App\Http\Requests\Recipe\ReparseRecipeByIdsRequest;
use App\Models\Recipe;
use App\Models\Source;
use App\Models\SourceRecipeUrl;
use App\Services\Parsers\RecipeParserFactory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class RecipeController extends Controller
{
    public function __construct(
        private readonly RecipeParserFactory $recipeParserFactory,
    )
    {
    }

    public function index(Request $request)
    {
        $query = Recipe::with(['steps', 'source']);

        if ($request->has('search') && !empty($request->get('search'))) {
            $query->where('title', 'like', '%' . $request->get('search') . '%');
        }

        if ($request->has('source') && !empty($request->get('source'))) {
            $query->whereHas('source', function ($query) use ($request) {
                $query->where('title', $request->source);
            });
        }

        if ($request->has('filter') && !empty($request->get('filter'))) {
            if ($request->filter === 'one_step') {
                $query->whereHas('steps', function ($q) {
                    $q->havingRaw('COUNT(*) = 1');
                }, '=');
            } elseif ($request->filter === 'one_ingredient') {
                $query->whereRaw('
                EXISTS (
                    SELECT 1 FROM recipe_ingredients
                    WHERE recipe_ingredients.recipe_id = recipes.id
                    GROUP BY recipe_ingredients.recipe_id
                    HAVING COUNT(*) = 1
                )
            ');
            }
        }

        $query->orderBy('created_at', 'desc');
        $recipes = $query->paginate(50);

        return view('pages/apps.recipe.recipes.list', compact('recipes'));
    }

    public function reparseByIds(ReparseRecipeByIdsRequest $request)
    {
        foreach ($request->get('recipe_ids') as $id) {
            Artisan::call('parse:recipe:id', [
                'recipeId' => $id,
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function deleteByIds(DeleteRecipeByIdsRequest $request)
    {
        foreach ($request->get('recipe_ids') as $id) {
            Recipe::destroy($id);
        }

        return response()->json(['success' => true]);
    }

    public function parseDebug(DebugRecipeParseRequest $request)
    {
        try {
            /** @var Source $source */
            $source = Source::find($request->get('source_id'));
            $parser = $this->recipeParserFactory->make($source->title);
            $xpath = $parser->loadHtml($request->get('url'));

            $recipe = [
                'title' => $parser->parseTitle($xpath),
                'image' => $parser->parseImage($xpath),
                'category' => $parser->parseCategory($xpath),
                'complexity' => $parser->parseComplexity($xpath),
                'cookingTime' => $parser->parseCookingTime($xpath),
                'portions' => $parser->parsePortions($xpath),
                'ingredients' => $parser->parseIngredients($xpath, true),
                'steps' => $parser->parseSteps($xpath),
            ];

            return response()->json([
                'success' => true,
                'recipe' => $recipe,
                'view' => view('pages.apps.recipe.recipes.partials.recipe-data', compact('recipe'))->render(),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function excludeRecipeUrl(SourceRecipeUrl $sourceRecipeUrl)
    {
        $sourceRecipeUrl->update([
            'is_excluded' => true,
        ]);

        return response()->json([
            'success' => true,
        ]);
    }
}
