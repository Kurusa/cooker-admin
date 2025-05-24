<?php

namespace App\Nova\Traits;

use Carbon\Carbon;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\DateTime;

trait NovaFieldMacros
{
    public static function formattedDate(string $name, string $attribute = null): Date
    {
        return Date::make($name, $attribute)
            ->displayUsing(fn($value) => $value ? Carbon::parse($value)->addHours(3)->format('d.m.Y H:i') : '—');
    }

    public static function formattedDateTime(string $name, string $attribute = null): DateTime
    {
        return DateTime::make($name, $attribute)
            ->displayUsing(fn($value) => $value ? Carbon::parse($value)->format('d.m.Y H:i') : '—');
    }
}
