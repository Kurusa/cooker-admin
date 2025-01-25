<?php

namespace App\Services\RecipeAttributes;

use App\DTO\StepDTO;
use App\Models\Recipe;
use App\Models\RecipeStep;

class StepService
{
    /**
     * @param StepDTO[] $steps
     * @param Recipe $recipe
     */
    public function attachSteps(array $steps, Recipe $recipe): void
    {
        $steps = collect($steps)
            ->unique(fn(StepDTO $step) => $step->description)
            ->filter();

        foreach ($steps as $step) {
            RecipeStep::create([
                'recipe_id'   => $recipe->id,
                'description' => $step->description,
                'image_url'   => $step->image,
            ]);
        }
    }
}
