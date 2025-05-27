<?php

namespace App\Services\RecipeAttributes;

use App\DTO\RecipeCuisineDTO;
use App\Models\Recipe\Recipe;
use App\Models\Recipe\RecipeCuisine;
use App\Notifications\DuplicateRecipeCuisineAttachNotification;
use Exception;
use Illuminate\Support\Facades\Notification;

class CuisineService
{
    /**
     * @param RecipeCuisineDTO[] $cuisines
     * @param Recipe $recipe
     */
    public function attachCuisines(array $cuisines, Recipe $recipe): void
    {
        $cuisines = collect($cuisines)
            ->unique(fn(RecipeCuisineDTO $cuisine) => $cuisine->title)
            ->filter();

        foreach ($cuisines as $cuisineData) {
            $cuisine = RecipeCuisine::updateOrCreate([
                'title' => $cuisineData->title,
            ], [
                'title' => $cuisineData->title,
            ]);

            try {
                $recipe->cuisines()->attach($cuisine->id);
            } catch (Exception $exception) {
                Notification::route('telegram', config('services.telegram.chat_id'))->notify(new DuplicateRecipeCuisineAttachNotification(
                    $recipe,
                    $cuisine,
                ));
                continue;
            }
        }
    }
}
