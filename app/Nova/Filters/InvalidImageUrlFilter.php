<?php

namespace App\Nova\Filters;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Laravel\Nova\Filters\BooleanFilter;

class InvalidImageUrlFilter extends BooleanFilter
{
    public $name = 'Image URL invalid';

    public function apply(Request $request, $query, $value): Builder
    {
        if ($value === 'with') {
            return $query->whereNotNull('image_url')
                ->where('image_url', '!=', '')
                ->where(function ($q) {
                    $q->where('image_url', 'not like', 'http://%')
                        ->where('image_url', 'not like', 'https://%');
                });
        }

        return $query;
    }

    public function options(Request $request): array
    {
        return [
            'with' => 'with',
        ];
    }
}
