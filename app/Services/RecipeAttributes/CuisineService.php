<?php

namespace App\Services\RecipeAttributes;

use App\DTO\CuisineDTO;
use App\Models\Cuisine;
use App\Models\Recipe\Recipe;
use App\Notifications\DuplicateRecipeCuisineAttachNotification;
use Exception;
use Illuminate\Support\Facades\Notification;

class CuisineService
{
    /**
     * @param CuisineDTO[] $cuisines
     * @param Recipe $recipe
     */
    public function attachCuisines(array $cuisines, Recipe $recipe): void
    {
        $cuisines = collect($cuisines)
            ->unique(fn(CuisineDTO $cuisine) => $cuisine->title)
            ->filter();

        foreach ($cuisines as $cuisineData) {
            $cuisine = Cuisine::updateOrCreate([
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
