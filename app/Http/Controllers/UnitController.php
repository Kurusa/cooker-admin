<?php

namespace App\Http\Controllers;

use App\Http\Requests\MergeUnitsRequest;
use App\Models\Ingredient\IngredientUnit;
use App\Models\Recipe\RecipeIngredient;
use App\Models\Unit;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class UnitController extends BaseController
{
    public function view(): View
    {
        /** @var Collection $units */
        $units = Unit::withCount('ingredientUnits')
            ->orderBy('title')
            ->get()
            ->map(function (Unit $unit) {
                return [
                    'id' => $unit->id,
                    'title' => $unit->title,
                    'ingredient_count' => $unit->ingredient_units_count,
                ];
            });

        return view('drag-units', [
            'units' => $units,
        ]);
    }

    public function merge(MergeUnitsRequest $request): JsonResponse
    {
        $mainUnitId = $request->get('main_unit_id');
        $unitIdsToMerge = $request->get('merge_unit_ids');

        DB::transaction(function () use ($mainUnitId, $unitIdsToMerge) {
            foreach ($unitIdsToMerge as $unitIdToMerge) {
                IngredientUnit::where('unit_id', $unitIdToMerge)
                    ->get()
                    ->each(function (IngredientUnit $ingredientUnit) use ($mainUnitId) {
                        $ingredientId = $ingredientUnit->ingredient_id;

                        $duplicate = IngredientUnit::where('ingredient_id', $ingredientId)
                            ->where('unit_id', $mainUnitId)
                            ->first();

                        if ($duplicate) {
                            RecipeIngredient::where('ingredient_unit_id', $ingredientUnit->id)
                                ->get()
                                ->each(function (RecipeIngredient $recipeIngredient) use ($duplicate) {
                                    $exists = RecipeIngredient::where('recipe_id', $recipeIngredient->recipe_id)
                                        ->where('ingredient_unit_id', $duplicate->id)
                                        ->where('quantity', $recipeIngredient->quantity)
                                        ->exists();

                                    if ($exists) {
                                        $recipeIngredient->delete();
                                    } else {
                                        $recipeIngredient->update([
                                            'ingredient_unit_id' => $duplicate->id,
                                        ]);
                                    }
                                });

                            $ingredientUnit->delete();
                        } else {
                            $ingredientUnit->update([
                                'unit_id' => $mainUnitId,
                            ]);
                        }
                    });

                Unit::where('id', $unitIdToMerge)->delete();
            }
        });

        return response()->json(['status' => 'ok']);
    }
}
