<?php

namespace App\Nova\Source;

use App\Models\Source\Source as SourceModel;
use App\Nova\Actions\Source\CollectSourceSitemapUrls;
use App\Nova\Actions\Source\ParseSourceRecipes;
use App\Nova\Recipe\Recipe;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Laravel\Nova\Resource;
use Laravel\Nova\Tabs\Tab;

class Source extends Resource
{
    public static string $model = SourceModel::class;

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
                ->displayUsing(fn($value) => "🔗 <a href=\"{$value}\" target=\"_blank\" style=\"color: #3490dc; text-decoration: underline;\">{$value}</a>")
                ->asHtml(),

            Text::make('Status', function () {
                $status = $this->getStatus();
                $color = $status->getBadgeColor();
                $label = $status->getLabel();
                return "<span style='background:{$color};color:white;padding:4px 8px;border-radius:6px;font-weight:600;font-size:12px;'>{$label}</span>";
            })->asHtml()->onlyOnIndex(),

            Tab::group('Details', [
                Tab::make('Relations', [
                    HasMany::make('Sitemaps recipe urls', 'recipeUrls', SourceRecipeUrl::class),
                    HasMany::make('Recipes', 'recipes', Recipe::class),
                    HasMany::make('Sitemaps', 'sitemaps', SourceSitemap::class),
                ]),
                Tab::make('Stats', [
                    new Panel('Stats', [
                        Number::make('Total URLs', fn() => $this->totalUrls()),
                        Number::make('Excluded URLs', fn() => $this->excludedUrlsCount()),
                        Number::make('Pending URLs', fn() => $this->pendingUrlsCount()),
                        Number::make('Parsed URLs', fn() => $this->parsedUrlsCount()),
                        Number::make('Parsed %', fn() => $this->percentageParsed())->displayUsing(fn($val) => $val . '%'),
                    ]),
                ]),
            ]),
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
