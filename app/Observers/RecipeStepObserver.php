<?php

namespace App\Observers;

use App\Enums\Source\SourceEnum;
use App\Models\Recipe\RecipeStep;

class RecipeStepObserver
{
    public function creating(RecipeStep $recipeStep): void
    {
        if (
            $recipeStep->recipe->source->title === SourceEnum::RUD->value
            && str_contains($recipeStep->description, "тм 'рудь'")
        ) {
            $recipeStep->description = str_replace("тм 'рудь'", '', $recipeStep->description);
        }

        if (
            $recipeStep->image_url
            && str_starts_with($recipeStep->image_url, 'files/')
            && $recipeStep->recipe->source->title === SourceEnum::SMACHNO->value
        ) {
            $recipeStep->image_url = 'https://www.smachno.in.ua/' . $recipeStep->image_url;
        }

        if (
            $recipeStep->image_url
            && str_starts_with($recipeStep->image_url, '/uploads')
            && $recipeStep->recipe->source->title === SourceEnum::YABPOELA->value
        ) {
            $recipeStep->image_url = 'https://ua.yabpoela.net' . $recipeStep->image_url;
        }

        if (
            $recipeStep->image_url
            && str_starts_with($recipeStep->image_url, 'uploads/')
            && $recipeStep->recipe->source->title === SourceEnum::YABPOELA->value
        ) {
            $recipeStep->image_url = 'https://ua.yabpoela.net/' . $recipeStep->image_url;
        }

        if (
            $recipeStep->image_url
            && str_starts_with($recipeStep->image_url, '/')
            && $recipeStep->recipe->source->title === SourceEnum::PICANTE->value
        ) {
            $recipeStep->image_url = 'https://picantecooking.com' . $recipeStep->image_url;
        }

        if (
            $recipeStep->image_url
            && str_starts_with($recipeStep->image_url, '/uploads')
            && $recipeStep->recipe->source->title === SourceEnum::FOODNET->value
        ) {
            $recipeStep->image_url = 'https://food.net.ua' . $recipeStep->image_url;
        }

        if (
            $recipeStep->image_url
            && (
                str_starts_with($recipeStep->image_url, '/uploads')
                || str_starts_with($recipeStep->image_url, '/foto')
                || str_starts_with($recipeStep->image_url, '/paska')
            )
            && $recipeStep->recipe->source->title === SourceEnum::SHEFKUHAR->value
        ) {
            $recipeStep->image_url = 'https://shefkuhar.com.ua' . $recipeStep->image_url;
        }
    }
}
