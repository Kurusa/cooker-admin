<?php

namespace App\Nova\Actions;

use App\Enums\Source\SourceRecipeUrlExcludedRuleType;
use App\Models\Recipe\Recipe;
use App\Models\Source\SourceRecipeUrlExcludedRule;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Actions\ActionResponse;
use Laravel\Nova\Fields\ActionFields;

class ExcludeRecipeUrl extends Action
{
    use InteractsWithQueue, Queueable;

    public $name = 'Exclude recipe url';

    public $showInline = true;

    public function handle(ActionFields $fields, Collection $models): ActionResponse
    {
        /** @var Recipe $model */
        foreach ($models as $model) {
            $model->sourceRecipeUrl->is_excluded = true;
            $model->sourceRecipeUrl->save();

            SourceRecipeUrlExcludedRule::create([
                'rule' => SourceRecipeUrlExcludedRuleType::EXACT,
                'source_id' => $model->source->id,
                'value' => $model->sourceRecipeUrl->url,
            ]);
        }

        return Action::message('Excluded recipe url.');
    }
}
