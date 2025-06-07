<?php

namespace App\Nova\Filters\SourceRecipeUrl;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Laravel\Nova\Filters\BooleanFilter;

class SourceRecipeUrlVerifiedFilter extends BooleanFilter
{
    public $name = 'Verified';

    public function apply(Request $request, $query, $value): Builder
    {
        if ($value['Verified'] ?? false) {
            return $query->verified();
        }

        if ($value['Not verified'] ?? false) {
            return $query->notVerified();
        }

        return $query;
    }

    public function options(Request $request): array
    {
        return [
            'Verified' => 'Verified',
            'Not verified' => 'Not verified',
        ];
    }
}
