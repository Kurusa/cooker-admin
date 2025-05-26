<?php

namespace App\Observers;

use App\Models\Recipe\Recipe;

class RecipeObserver
{
    public function creating(Recipe $recipe): void
    {
        if (
            $recipe->image_url
            && str_starts_with($recipe->image_url, '/')
            && $recipe->source->title === 'rud'
        ) {
            $recipe->image_url = 'https://rud.ua' . $recipe->image_url;
        }

        if (
            $recipe->image_url
            && str_starts_with($recipe->image_url, '/')
            && $recipe->source->title === 'jisty'
        ) {
            $recipe->image_url = 'https://jisty.com.ua' . $recipe->image_url;
        }

        if (
            $recipe->image_url
            && str_contains($recipe->image_url, '-150x150.')
            && $recipe->source->title === 'novastrava'
        ) {
            $recipe->image_url = str_replace('-150x150.', '.', $recipe->image_url);
        }
    }
}
