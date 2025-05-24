<?php

namespace App\Nova\Source;

use App\Models\Source\SourceRecipeUrl as SourceRecipeUrlModel;
use App\Nova\Actions\Source\ExcludeSourceRecipeUrl;
use App\Nova\Actions\Source\ParseRecipeByUrl;
use App\Nova\Filters\SourceFilter;
use App\Nova\Filters\SourceRecipeUrl\SourceRecipeUrlParsedFilter;
use App\Nova\Recipe\Recipe;
use App\Nova\Resource;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class SourceRecipeUrl extends Resource
{
    public static string $model = SourceRecipeUrlModel::class;

    public static $title = 'url';

    public static $group = 'Sources';

    public static $search = [
        'id',
        'url',
    ];

    public function fields(Request $request): array
    {
        if (!$this->recipe()->exists()) {
            $iframe = '<iframe src="' . $this->url . '" width="100%" height="500" style="border:1px solid #ccc;"></iframe>';
        }

        return [
            ID::make()->sortable(),

            BelongsTo::make('Source', 'source', Source::class)
                ->sortable(),

            Text::make('URL', 'url')->sortable()->rules('required', 'url')
                ->displayUsing(fn($value) => "<a href=\"{$value}\" target=\"_blank\">{$value}</a>")
                ->asHtml(),

            Boolean::make('Is Parsed', fn() => $this->recipe()->exists())
                ->exceptOnForms(),

            Boolean::make('Is Excluded', 'is_excluded'),

            Heading::make($iframe ?? '')
                ->asHtml()
                ->onlyOnDetail(),

            HasOne::make('Recipe', 'recipe', Recipe::class)
        ];
    }

    public function filters(NovaRequest $request): array
    {
        return [
            new SourceFilter,
            new SourceRecipeUrlParsedFilter,
        ];
    }

    public function actions(NovaRequest $request): array
    {
        return [
            new ExcludeSourceRecipeUrl,
            new ParseRecipeByUrl,
        ];
    }
}
