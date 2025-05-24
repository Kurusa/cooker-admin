<?php

namespace App\Services;

use App\Models\Source\Source;

class FindSourceByTitle
{
    public static function find(string $title): Source
    {
        return Source::where('title', $title)->first();
    }
}
