<?php

namespace App\Core;

class KTBootstrap
{
    public static function init(): void
    {
        KTBootstrap::initThemeMode();
        KTBootstrap::initThemeDirection();
        KTBootstrap::initLayout();
    }

    public static function initThemeMode(): void
    {
        setModeSwitch(config('settings.KT_THEME_MODE_SWITCH_ENABLED'));
        setModeDefault(config('settings.KT_THEME_MODE_DEFAULT'));
    }

    public static function initThemeDirection(): void
    {
        setDirection(config('settings.KT_THEME_DIRECTION'));

        if (isRtlDirection()) {
            addHtmlAttribute('html', 'direction', 'rtl');
            addHtmlAttribute('html', 'dir', 'rtl');
            addHtmlAttribute('html', 'style', 'direction: rtl');
        }
    }

    public static function initLayout(): void
    {
        addHtmlAttribute('body', 'id', 'kt_app_body');
        addHtmlAttribute('body', 'data-kt-name', getName());
    }
}
