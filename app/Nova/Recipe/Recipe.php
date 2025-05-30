<?php

namespace App\Nova\Recipe;

use App\Models\Recipe\Recipe as RecipeModel;
use App\Nova\Actions\Source\VerifySourceRecipeUrl;
use App\Nova\Filters\InvalidImageUrlFilter;
use App\Nova\Filters\Recipe\RecipeHasOneIngredientOrStepFilter;
use App\Nova\Filters\Recipe\RecipeWithoutCuisineFilter;
use App\Nova\Filters\SourceFilter;
use App\Nova\Ingredient\IngredientGroup;
use App\Nova\Resource;
use App\Nova\Source\SourceRecipeUrl;
use App\Nova\Traits\NovaFieldMacros;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Avatar;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Lupennat\ExpandableMany\HasExpandableMany;

class Recipe extends Resource
{
    use NovaFieldMacros, HasExpandableMany;

    public static string $model = RecipeModel::class;

    public static $title = 'title';

    public static $search = [
        'id',
        'title',
    ];

    public function fields(Request $request): array
    {
        return [
            ID::make()->sortable(),

            Boolean::make('Is verified'),

            Avatar::make('Image')->thumbnail(function () {
                return $this->image_url;
            }),

            Text::make('image_url')
                ->onlyOnDetail(),

            Text::make('Title')
                ->sortable(),

            Text::make('Categories', function () {
                return $this->categories->map(function ($category) {
                    return "<span style='display: inline-block; background: #f1f5f9; color: #0f172a; border-radius: 12px; padding: 2px 10px; margin: 0 4px 4px 0; font-size: 12px; font-weight: 500;'>{$category->title}</span>";
                })->implode('');
            })->asHtml(),

            Text::make('Complexity', function () {
                $label = ucfirst($this->complexity?->value);
                $color = $this->complexity?->getBadgeColor();
                return "<span style='background:{$color};color:white;padding:4px 8px;border-radius:6px;font-weight:600;font-size:12px;'>{$label}</span>";
            })->asHtml(),

            Number::make('Time')->help('Minutes'),

            Number::make('Portions'),

            BelongsTo::make('Source', 'source'),

            BelongsTo::make('Source recipe url id', 'sourceRecipeUrl', SourceRecipeUrl::class),

            Text::make('Source recipe url', function () {
                return "🔗 <a href=\"{$this->url}\" target=\"_blank\" style=\"color: #3490dc; text-decoration: underline;\">{$this->url}</a>";
            })->asHtml(),

            Heading::make("<iframe src=\"{$this->url}\" width=\"100%\" height=\"500\" style=\"border:1px solid #ccc;\"></iframe>")
                ->asHtml()
                ->onlyOnDetail(),

            HasMany::make('Ingredient Groups', 'ingredientGroups', IngredientGroup::class),

            HasMany::make('Steps', 'steps', RecipeStep::class),
            //BelongsToMany::make('Cuisines', 'cuisines', Cuisine::class),

            self::formattedDateTime('Created at'),
        ];
    }

    public function filters(NovaRequest $request): array
    {
        return [
            new SourceFilter,
            new InvalidImageUrlFilter,
            new RecipeHasOneIngredientOrStepFilter,
            new RecipeWithoutCuisineFilter,
        ];
    }

    public function actions(NovaRequest $request): array
    {
        return [
            new VerifySourceRecipeUrl,
        ];
    }
}
