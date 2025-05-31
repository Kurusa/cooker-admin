<?php

namespace App\Nova\Source;

use App\Models\Source\SourceRecipeUrl as SourceRecipeUrlModel;
use App\Nova\Actions\Source\CheckSelectedUrlsForExclusion;
use App\Nova\Actions\Source\ExcludeSourceRecipeUrl;
use App\Nova\Actions\Source\ParseRecipeByUrl;
use App\Nova\Actions\Source\VerifySourceRecipeUrl;
use App\Nova\Filters\SourceFilter;
use App\Nova\Filters\SourceRecipeUrl\SourceRecipeUrlExcludedFilter;
use App\Nova\Filters\SourceRecipeUrl\SourceRecipeUrlHasMoreThanOneRecipeFilter;
use App\Nova\Filters\SourceRecipeUrl\SourceRecipeUrlParsedFilter;
use App\Nova\Recipe\Recipe;
use App\Nova\Resource;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class SourceRecipeUrl extends Resource
{
    public static string $model = SourceRecipeUrlModel::class;

    public static $perPageViaRelationship = 20;

    public static $search = [
        'url',
    ];

    public function fields(Request $request): array
    {
        if (!$this->recipes()->exists()) {
            $iframe = '<iframe src="' . $this->url . '" width="100%" height="500" style="border:1px solid #ccc;"></iframe>';
        }

        return [
            ID::make()->sortable(),

            BelongsTo::make('Source', 'source', Source::class)
                ->sortable(),

            Text::make('URL', 'url')->sortable()
                ->displayUsing(fn($value) => "🔗 <a href=\"{$value}\" target=\"_blank\" style=\"color: #3490dc; text-decoration: underline;\">{$value}</a>")
                ->asHtml(),

            Boolean::make('Is verified'),

            Boolean::make('Is parsed', fn() => $this->recipes()->exists())
                ->exceptOnForms(),

            Boolean::make('Is excluded', 'is_excluded'),

            Heading::make($iframe ?? '')
                ->asHtml()
                ->onlyOnDetail(),

            HasMany::make('Recipes', 'recipes', Recipe::class)
        ];
    }

    public function filters(NovaRequest $request): array
    {
        return [
            new SourceFilter,
            new SourceRecipeUrlHasMoreThanOneRecipeFilter,
            new SourceRecipeUrlParsedFilter,
            new SourceRecipeUrlExcludedFilter,
        ];
    }

    public function actions(NovaRequest $request): array
    {
        return [
            new VerifySourceRecipeUrl,
            new ExcludeSourceRecipeUrl,
            new ParseRecipeByUrl,
            new CheckSelectedUrlsForExclusion,
        ];
    }

    public static function authorizedToCreate(Request $request): bool
    {
        return false;
    }
}
