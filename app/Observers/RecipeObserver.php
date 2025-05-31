<?php

namespace App\Observers;

use App\Enums\Source\SourceEnum;
use App\Models\Recipe\Recipe;

class RecipeObserver
{
    public function creating(Recipe $recipe): void
    {
        if (
            $recipe->image_url
            && str_starts_with($recipe->image_url, '/')
            && $recipe->source->title === SourceEnum::RUD->value
        ) {
            $recipe->image_url = 'https://rud.ua' . $recipe->image_url;
        }

        if (
            $recipe->image_url
            && str_starts_with($recipe->image_url, '/')
            && $recipe->source->title === SourceEnum::JISTY->value
        ) {
            $recipe->image_url = 'https://jisty.com.ua' . $recipe->image_url;
        }

        if (
            $recipe->image_url
            && str_contains($recipe->image_url, '-150x150.')
            && $recipe->source->title === SourceEnum::NOVASTRAVA->value
        ) {
            $recipe->image_url = str_replace('-150x150.', '.', $recipe->image_url);
        }

        if (
            $recipe->image_url
            && str_starts_with($recipe->image_url, '/')
            && $recipe->source->title === SourceEnum::PICANTE->value
        ) {
            $recipe->image_url = 'https://picantecooking.com' . $recipe->image_url;
        }

        if (
            $recipe->image_url
            && str_starts_with($recipe->image_url, 'files/')
            && $recipe->source->title === SourceEnum::SMACHNO->value
        ) {
            $recipe->image_url = 'https://www.smachno.in.ua/' . $recipe->image_url;
        }

        if (
            $recipe->image_url
            && str_starts_with($recipe->image_url, 'uploads/')
            && $recipe->source->title === SourceEnum::YABPOELA->value
        ) {
            $recipe->image_url = 'https://ua.yabpoela.net/' . $recipe->image_url;
        }

        if (
            $recipe->image_url
            && str_starts_with($recipe->image_url, '/uploads')
            && $recipe->source->title === SourceEnum::YABPOELA->value
        ) {
            $recipe->image_url = 'https://ua.yabpoela.net' . $recipe->image_url;
        }

        if (
            $recipe->image_url
            && str_starts_with($recipe->image_url, '/uploads')
            && $recipe->source->title === SourceEnum::FOODNET->value
        ) {
            $recipe->image_url = 'https://food.net.ua' . $recipe->image_url;
        }

        if (
            $recipe->image_url
            && (
                str_starts_with($recipe->image_url, '/uploads')
                || str_starts_with($recipe->image_url, '/foto')
                || str_starts_with($recipe->image_url, '/paska')
            )
            && $recipe->source->title === SourceEnum::SHEFKUHAR->value
        ) {
            $recipe->image_url = 'https://shefkuhar.com.ua' . $recipe->image_url;
        }
    }
}
