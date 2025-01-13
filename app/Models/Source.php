<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Source extends Model
{
    protected $fillable = [
        'url',
    ];

    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class, 'source_id');
    }
}
