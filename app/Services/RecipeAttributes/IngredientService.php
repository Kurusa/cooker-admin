<?php

namespace App\Services\RecipeAttributes;

use App\DTO\IngredientGroupDTO;
use App\Models\Ingredient\Ingredient;
use App\Models\Ingredient\IngredientUnit;
use App\Models\Recipe\Recipe;
use App\Models\Unit;

class IngredientService
{
    /**
     * @param IngredientGroupDTO[] $ingredientGroups
     * @param Recipe $recipe
     */
    public function attachIngredientGroups(array $ingredientGroups, Recipe $recipe): void
    {
        foreach ($ingredientGroups as $groupDTO) {
            $groupTitle = $groupDTO->group ?: 'загальні інгредієнти';

            $groupModel = $recipe->ingredientGroups()->firstOrCreate([
                'title' => $groupTitle,
            ]);

            foreach ($groupDTO->ingredients as $ingredientData) {
                $ingredient = Ingredient::firstOrCreate([
                    'title' => $ingredientData->title,
                ]);

                $unit = $this->getUnit($ingredientData->unit);

                $ingredientUnit = IngredientUnit::firstOrCreate([
                    'ingredient_id' => $ingredient->id,
                    'unit_id' => $unit?->id,
                ]);

                $recipe->ingredients()->attach($ingredientUnit->id, [
                    'quantity' => $ingredientData->quantity,
                    'ingredient_group_id' => $groupModel?->id,
                ]);
            }
        }
    }

    private function getUnit(?string $unitTitle): ?Unit
    {
        return $unitTitle ? Unit::firstOrCreate(['title' => $unitTitle]) : null;
    }
}
