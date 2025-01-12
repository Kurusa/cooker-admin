<?php

namespace App\View\Components;

use Illuminate\View\Component;

class AuthLayout extends Component
{
    public function __construct()
    {
        app(config('settings.KT_THEME_BOOTSTRAP.auth'))->init();
    }

    public function render()
    {
        return view(config('settings.KT_THEME_LAYOUT_DIR') . '._auth');
    }
}
