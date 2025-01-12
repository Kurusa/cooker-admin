<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

class EncryptCookies extends Middleware
{
    protected $except = [
        "sidebar_minimize_state",
        "kt_aside_toggle_state",
        "kt_aside_menu",
        "data-kt-app-sidebar-minimize",
    ];
}
