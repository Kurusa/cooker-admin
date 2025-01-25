<?php

namespace App\Services\RecipeAttributes;

use App\DTO\IngredientDTO;
use App\Models\Ingredient;
use App\Models\IngredientUnit;
use App\Models\Recipe;
use App\Models\Unit;

class IngredientService
{
    /**
     * @param IngredientDTO[] $ingredients
     * @param Recipe $recipe
     */
    public function attachIngredients(array $ingredients, Recipe $recipe): void
    {
        foreach ($ingredients as $ingredientData) {
            /** @var Ingredient $ingredient */
            $ingredient = Ingredient::firstOrCreate([
                'title'          => $ingredientData->title,
                'original_title' => $ingredientData->originalTitle,
            ]);

            $unit = $this->getUnit($ingredientData->unit);

            /** @var IngredientUnit $ingredientUnit */
            $ingredientUnit = IngredientUnit::firstOrCreate([
                'ingredient_id' => $ingredient->id,
                'unit_id'       => $unit?->id,
            ]);

            $recipe->ingredients()->attach($ingredientUnit->id, [
                'quantity'         => $ingredientData->quantity,
                'ingredient_title' => $ingredient->title,
                'unit_title'       => $ingredientUnit->title,
            ]);
        }
    }

    private function getUnit(?string $unitTitle): ?Unit
    {
        return $unitTitle ? Unit::firstOrCreate(['title' => $unitTitle]) : null;
    }
}
