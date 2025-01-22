<?php

namespace App\Services\RecipeAttributes;

use App\Models\Recipe;
use App\Models\Step;

class StepService
{
    public function attachSteps(array $steps, Recipe $recipe): void
    {
        $steps = array_unique(array_filter($steps), SORT_REGULAR);

        foreach ($steps as $step) {
            Step::create([
                'recipe_id' => $recipe->id,
                'description' => $step['description'] ?? $step,
                'image_url' => $step['imageUrl'] ?? '',
            ]);
        }
    }
}
