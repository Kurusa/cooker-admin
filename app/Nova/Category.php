<?php

namespace App\Nova;

use App\Models\Category as CategoryModel;
use App\Nova\Recipe\Recipe;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Resource;

class Category extends Resource
{
    public static string $model = CategoryModel::class;

    public static $title = 'title';

    public static $group = 'Recipes';

    public static $search = [
        'id',
        'title',
    ];

    public function fields(Request $request): array
    {
        return [
            ID::make()->sortable(),

            Text::make('Title')
                ->sortable()
                ->rules('required', 'max:255'),

            BelongsTo::make('Parent category', 'parent', self::class)
                ->nullable(),

            HasMany::make('Child categories', 'children', self::class),

            Number::make('Recipes count', function () {
                return $this->recipes()->count();
            })->onlyOnDetail(),

            BelongsToMany::make('Recipes', 'recipes', Recipe::class),
        ];
    }
}
