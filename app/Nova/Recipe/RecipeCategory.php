<?php

namespace App\Nova\Recipe;

use App\Models\Recipe\RecipeCategory as CategoryModel;
use App\Nova\Actions\MergeRecipeCategories;
use App\Nova\Filters\HasChildrenRecipeCategories;
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
use Lupennat\ExpandableMany\HasExpandableMany;

class RecipeCategory extends Resource
{
    use NovaFieldMacros, HasExpandableMany;

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

            Number::make('Recipes count', fn() => $this->recipes_with_children_count),

            BelongsToMany::make('Recipes', 'recipes', Recipe::class),

            BelongsToMany::make('Parent Categories', 'parents', self::class),

            BelongsToMany::make('Child Categories', 'children', self::class)
                ->expandable(function (BelongsToMany $field, $resource) {
                    $resource->loadCount('children');
                    $field->withMeta([
                        'expandableShowLabel' => 'Show ' . $resource->children_count . ' children',
                    ]);
                }),

            self::formattedDateTime('Created at'),
        ];
    }

    public function actions(NovaRequest $request): array
    {
        return [
            new MergeRecipeCategories,
        ];
    }

    public function filters(NovaRequest $request): array
    {
        return [
            new HasChildrenRecipeCategories,
        ];
    }
}
