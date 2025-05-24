<?php

namespace App\Nova\Source;

use App\Models\Source\Source as SourceModel;
use App\Nova\Actions\Source\CollectSourceSitemapUrls;
use App\Nova\Actions\Source\ParseSourceRecipes;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Laravel\Nova\Resource;

class Source extends Resource
{
    public static string $model = SourceModel::class;

    public static $group = 'Sources';

    public static $title = 'title';

    public static $search = [
        'id',
        'title',
        'url',
    ];

    public function fields(Request $request): array
    {
        return [
            ID::make()->sortable(),

            Text::make('Title')->sortable()->rules('required', 'max:255'),

            Text::make('URL')->sortable()->rules('required', 'url')
                ->displayUsing(fn($value) => "<a href=\"{$value}\" target=\"_blank\">{$value}</a>")
                ->asHtml(),

            new Panel('Stats', [
                Number::make('Total URLs', fn() => $this->totalUrls()),
                Number::make('Parsed URLs', fn() => $this->parsedUrlsCount()),
                Number::make('Pending URLs', fn() => $this->pendingUrlsCount()),
                Number::make('Excluded URLs', fn() => $this->excludedUrlsCount()),
                Number::make('Parsed %', fn() => $this->percentageParsed())->displayUsing(fn($val) => $val . '%'),
            ]),

            HasMany::make('Sitemaps', 'sitemaps', SourceSitemap::class),

            HasMany::make('Sitemaps recipe urls', 'recipeUrls', SourceRecipeUrl::class),
        ];
    }

    public function actions(NovaRequest $request): array
    {
        return [
            new CollectSourceSitemapUrls,
            new ParseSourceRecipes,
        ];
    }
}
