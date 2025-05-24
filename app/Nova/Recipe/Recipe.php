<?php

namespace App\Nova\Recipe;

use App\Enums\Recipe\Complexity;
use App\Models\Recipe\Recipe as RecipeModel;
use App\Nova\Actions\ExcludeRecipeUrl;
use App\Nova\Actions\Source\ParseRecipeByUrl;
use App\Nova\Category;
use App\Nova\Cuisine;
use App\Nova\Filters\RecipeHasOneIngredientOrStep;
use App\Nova\Resource;
use App\Nova\Traits\NovaFieldMacros;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Avatar;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class Recipe extends Resource
{
    use NovaFieldMacros;

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

            Text::make('Complexity', function () {
                $label = ucfirst($this->complexity?->value);
                $color = $this->complexity?->getBadgeColor();

                return "<span style='background:{$color};color:white;padding:4px 8px;border-radius:6px;font-weight:600;font-size:12px;'>{$label}</span>";
            })->asHtml(),

            Select::make('Complexity')
                ->options(collect(Complexity::cases())->mapWithKeys(fn($case) => [
                    $case->value => $case->getEmoji() . ' ' . ucfirst($case->value)
                ])->toArray())
                ->rules('required')
                ->displayUsingLabels()
                ->onlyOnForms(),

            Textarea::make('Advice'),

            Number::make('Time')
                ->help('Minutes'),

            Number::make('Portions'),

            BelongsTo::make('Source', 'source')
                ->exceptOnForms(),

            HasMany::make('Ingredients', 'recipeIngredients', RecipeIngredient::class),

            HasMany::make('Steps', 'steps', RecipeStep::class),

            HasMany::make('Cuisines', 'cuisines', Cuisine::class),

            HasMany::make('Categories', 'categories', Category::class),

            Heading::make("<iframe src=\"{$this->sourceRecipeUrl?->url}\" width=\"100%\" height=\"500\" style=\"border:1px solid #ccc;\"></iframe>")
                ->asHtml()
                ->onlyOnDetail(),

            self::formattedDateTime('Created at'),
        ];
    }

    public function filters(NovaRequest $request): array
    {
        return [
            new RecipeHasOneIngredientOrStep,
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
