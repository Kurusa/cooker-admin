<?php

namespace App\Nova\Recipe;

use App\Models\Recipe\RecipeCategory as CategoryModel;
use App\Nova\Actions\MergeRecipeCategories;
use App\Nova\Filters\HasChildrenRecipeCategories;
use App\Nova\Filters\OnlyRootCategories;
use App\Nova\Traits\NovaFieldMacros;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsToMany;
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

            BelongsToMany::make('Recipes', 'recipes', Recipe::class),

            Text::make('Parents', function () {
                return $this->parents->map(function (CategoryModel $parent) {
                    return "<span style='display:inline-block;
                                   background:#f1f5f9;
                                   color:#0f172a;
                                   border-radius:12px;
                                   padding:2px 10px;
                                   margin:0 4px 4px 0;
                                   font-size:12px;
                                   font-weight:500;'>{$parent->title}</span>";
                })->implode('');
            })->asHtml()->onlyOnIndex(),

            BelongsToMany::make('Childs', 'children', self::class)
                ->expandable(function (BelongsToMany $field, $resource) {
                    $resource->loadCount('children');
                    $field->withMeta([
                        'expandableShowLabel' => 'Show ' . $resource->children_count . ' children',
                    ]);
                }),

            Number::make('Recipes count', fn() => $this->recipes_with_children_count),

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
            new OnlyRootCategories,
        ];
    }
}
