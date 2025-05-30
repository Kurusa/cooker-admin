<?php

namespace App\Nova\Source;

use App\Models\Source\SourceSitemap as SourceSitemapModel;
use App\Nova\Resource;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;

class SourceSitemap extends Resource
{
    public static string $model = SourceSitemapModel::class;

    public static $title = 'url';

    public static $search = [
        'id',
        'url',
    ];

    public function fields(Request $request): array
    {
        return [
            ID::make()->sortable(),

            BelongsTo::make('Source', 'source', Source::class)
                ->sortable(),

            Text::make('URL', 'url')->sortable()
                ->displayUsing(fn($value) => "🔗 <a href=\"{$value}\" target=\"_blank\" style=\"color: #3490dc; text-decoration: underline;\">{$value}</a>")
                ->asHtml(),
        ];
    }
}
