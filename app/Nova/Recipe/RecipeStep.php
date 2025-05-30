<?php

namespace App\Nova\Recipe;

use App\Models\Recipe\RecipeStep as RecipeStepModel;
use App\Nova\Filters\InvalidImageUrlFilter;
use App\Nova\Resource;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Avatar;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class RecipeStep extends Resource
{
    public static string $model = RecipeStepModel::class;

    public static $title = 'description';

    public static $search = [
        'id',
        'description',
    ];

    public function fields(Request $request): array
    {
        return [
            ID::make()->sortable(),

            BelongsTo::make('Recipe', 'recipe', Recipe::class)
                ->sortable(),

            Avatar::make('Image Preview')
                ->thumbnail(fn() => $this->image_url)
                ->preview(fn() => $this->image_url)
                ->exceptOnForms(),

            Text::make('Description'),

            Text::make('Image URL', 'image_url')
                ->hideFromIndex(),
        ];
    }

    public function filters(NovaRequest $request): array
    {
        return [
            new InvalidImageUrlFilter,
        ];
    }
}
