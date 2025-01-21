<?php

namespace App\Services\RecipeAttributes;

use App\Models\Ingredient;
use App\Models\IngredientUnit;
use App\Models\Recipe;
use App\Models\Unit;

class IngredientService
{
    public function attachIngredients(array $ingredients, Recipe $recipe): void
    {
        foreach ($ingredients as $ingredientData) {
            if (!strlen($ingredientData['title'])) {
                continue;
            }

            $ingredient = Ingredient::firstOrCreate(['title' => $ingredientData['title']]);

            $unit = $this->getUnit($ingredientData['unit'] ?? null);

            $ingredientUnit = IngredientUnit::firstOrCreate([
                'ingredient_id' => $ingredient->id,
                'unit_id' => $unit?->id,
            ]);

            $recipe->ingredients()->attach($ingredientUnit->id, [
                'quantity' => $ingredientData['quantity'] ?? null,
            ]);
        }
    }

    private function getUnit(?string $unitTitle): ?Unit
    {
        return $unitTitle ? Unit::firstOrCreate(['title' => $unitTitle]) : null;
    }
}
