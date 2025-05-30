<?php

namespace App\Nova\Recipe;

use App\Models\Recipe\RecipeCategory as CategoryModel;
use App\Nova\Actions\MergeRecipeCategories;
use App\Nova\Traits\NovaFieldMacros;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Resource;

class RecipeCategory extends Resource
{
    use NovaFieldMacros;

    public static string $model = CategoryModel::class;

    public static $title = 'title';

    public static $search = [
        'id',
        'title',
    ];

    public function fields(Request $request): array
    {
        return [
            ID::make()->sortable(),

            Text::make('Title')
                ->sortable(),

            Number::make('Recipes count', function () {
                return $this->recipes()->count();
            }),

            BelongsToMany::make('Recipes', 'recipes', Recipe::class),

            BelongsTo::make('Parent category', 'parent', self::class)
                ->nullable(),

            HasMany::make('Child categories', 'children', self::class),

            Text::make('Related Categories', function () {
                return $this->relatedCategories()
                    ->pluck('title')
                    ->unique()
                    ->implode(', ');
            })->onlyOnDetail(),

            self::formattedDateTime('Created at'),
        ];
    }

    public function actions(NovaRequest $request): array
    {
        return [
            new MergeRecipeCategories,
        ];
    }
}
