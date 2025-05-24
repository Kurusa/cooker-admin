<?php

namespace App\Nova\Actions\Source;

use App\Models\Source\Source;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
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
        /** @var Source $model */
        foreach ($models as $model) {
            Artisan::call('collect:source:sitemap-urls', [
                'sourceId' => $model->id,
            ]);
        }

        return Action::message('Collection started.');
    }
}
