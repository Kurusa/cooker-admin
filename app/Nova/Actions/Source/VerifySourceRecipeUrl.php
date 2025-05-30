<?php

namespace App\Nova\Actions\Source;

use App\Models\Source\SourceRecipeUrl;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Actions\ActionResponse;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Http\Requests\NovaRequest;

class VerifySourceRecipeUrl extends Action
{
    public $name = 'Set verified';

    public function handle(ActionFields $fields, Collection $models): ActionResponse
    {
        foreach ($models as $model) {
            if ($model instanceof SourceRecipeUrl) {
                $model->is_verified = $fields->verified;
                $model->save();
            }
        }

        return Action::message('Verification status updated.');
    }

    public function fields(NovaRequest $request): array
    {
        return [
            Boolean::make('Verified', 'verified')
        ];
    }
}
