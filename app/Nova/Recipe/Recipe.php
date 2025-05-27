<?php

namespace App\Nova\Recipe;

use App\Models\Recipe\Recipe as RecipeModel;
use App\Nova\Actions\ExcludeRecipeUrl;
use App\Nova\Actions\Source\ParseRecipeByUrl;
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
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Tabs\Tab;
use Lupennat\ExpandableMany\HasExpandableMany;

class Recipe extends Resource
{
    use NovaFieldMacros, HasExpandableMany;

    public static string $model = RecipeModel::class;

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

            Avatar::make('Image')->thumbnail(function () {
                return $this->image_url;
            }),

            Text::make('Title')
                ->sortable()
                ->rules('required', 'max:255'),

            BelongsToMany::make('Categories', 'categories', RecipeCategory::class)
                ->expandable()
                ->withMeta([
                    'expandableShowLabel' => 'Show categories',
                    'expandableHideLabel' => 'Hide Categories',
                ]),

            Heading::make("<iframe src=\"{$this->sourceRecipeUrl?->url}\" width=\"100%\" height=\"500\" style=\"border:1px solid #ccc;\"></iframe>")
                ->asHtml()
                ->onlyOnDetail(),

            Tab::group('Details', [
                Tab::make('Relations', [
                    HasMany::make('Ingredients', 'recipeIngredients', RecipeIngredient::class),
                    HasMany::make('Ingredient Groups', 'ingredientGroups', IngredientGroup::class),
                    HasMany::make('Steps', 'steps', RecipeStep::class),
                    //BelongsToMany::make('Cuisines', 'cuisines', Cuisine::class),
                ]),
                Tab::make('Main', [
                    Text::make('Complexity', function () {
                        $label = ucfirst($this->complexity?->value);
                        $color = $this->complexity?->getBadgeColor();
                        return "<span style='background:{$color};color:white;padding:4px 8px;border-radius:6px;font-weight:600;font-size:12px;'>{$label}</span>";
                    })->asHtml(),
                    Number::make('Time')->help('Minutes'),
                    Number::make('Portions'),
                    Text::make('image_url')->onlyOnDetail(),
                    BelongsTo::make('Source', 'source'),
                    BelongsTo::make('Source recipe url', 'sourceRecipeUrl', SourceRecipeUrl::class),
                ]),
            ]),

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
            new ParseRecipeByUrl,
            new ExcludeRecipeUrl,
        ];
    }
}
