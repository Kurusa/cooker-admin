<?php

namespace App\Services\RecipeAttributes;

use App\Models\Recipe;
use App\Models\Step;

class StepService
{
    public function attachSteps(array $steps, Recipe $recipe): void
    {
        foreach ($steps as $step) {
            if (!$step || (isset($step['description']) && !mb_strlen($step['description']))) {
                continue;
            }

            Step::create([
                'recipe_id' => $recipe->id,
                'description' => $step['description'] ?? $step,
                'image_url' => $step['imageUrl'] ?? '',
            ]);
        }
    }
}
