<?php

namespace App\Nova\Actions\Source;

use App\Models\Source\Source;
use App\Services\CollectSitemapUrlsService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Actions\ActionResponse;
use Laravel\Nova\Fields\ActionFields;

class CollectSourceSitemapUrls extends Action
{
    use InteractsWithQueue, Queueable;

    public $name = 'Collect source sitemap urls';

    public $showInline = true;

    public function handle(ActionFields $fields, Collection $models): ActionResponse
    {
        /** @var CollectSitemapUrlsService $collectSitemapUrlsService */
        $collectSitemapUrlsService = app(CollectSitemapUrlsService::class);

        /** @var Source $source */
        foreach ($models as $source) {
            $collectSitemapUrlsService->collectSitemapUrls($source);
        }

        return Action::message('Collection finished.');
    }
}
