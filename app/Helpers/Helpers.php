<?php

if (!function_exists('theme')) {
    function theme()
    {
        return app(App\Core\Theme::class);
    }
}


if (!function_exists('getName')) {
    function getName()
    {
        return config('settings.KT_THEME');
    }
}


if (!function_exists('addHtmlAttribute')) {
    function addHtmlAttribute($scope, $name, $value): void
    {
        theme()->addHtmlAttribute($scope, $name, $value);
    }
}


if (!function_exists('addHtmlAttributes')) {
    function addHtmlAttributes($scope, $attributes)
    {
        theme()->addHtmlAttributes($scope, $attributes);
    }
}


if (!function_exists('addHtmlClass')) {
    function addHtmlClass($scope, $value)
    {
        theme()->addHtmlClass($scope, $value);
    }
}


if (!function_exists('printHtmlAttributes')) {
    function printHtmlAttributes($scope)
    {
        return theme()->printHtmlAttributes($scope);
    }
}


if (!function_exists('printHtmlClasses')) {
    function printHtmlClasses($scope, $full = true)
    {
        return theme()->printHtmlClasses($scope, $full);
    }
}


if (!function_exists('getSvgIcon')) {
    function getSvgIcon($path, $classNames = 'svg-icon', $folder = 'assets/media/icons/')
    {
        return theme()->getSvgIcon($path, $classNames, $folder);
    }
}


if (!function_exists('setModeSwitch')) {
    function setModeSwitch($flag)
    {
        theme()->setModeSwitch($flag);
    }
}


if (!function_exists('isModeSwitchEnabled')) {
    function isModeSwitchEnabled()
    {
        return theme()->isModeSwitchEnabled();
    }
}


if (!function_exists('setModeDefault')) {
    function setModeDefault($mode)
    {
        theme()->setModeDefault($mode);
    }
}


if (!function_exists('getModeDefault')) {
    function getModeDefault()
    {
        return theme()->getModeDefault();
    }
}


if (!function_exists('setDirection')) {
    function setDirection($direction)
    {
        theme()->setDirection($direction);
    }
}


if (!function_exists('getDirection')) {
    function getDirection()
    {
        return theme()->getDirection();
    }
}


if (!function_exists('isRtlDirection')) {
    function isRtlDirection()
    {
        return theme()->isRtlDirection();
    }
}


if (!function_exists('extendCssFilename')) {
    function extendCssFilename($path)
    {
        return theme()->extendCssFilename($path);
    }
}


if (!function_exists('includeFavicon')) {
    function includeFavicon()
    {
        return theme()->includeFavicon();
    }
}


if (!function_exists('includeFonts')) {
    function includeFonts()
    {
        return theme()->includeFonts();
    }
}


if (!function_exists('getGlobalAssets')) {
    function getGlobalAssets($type = 'js')
    {
        return theme()->getGlobalAssets($type);
    }
}


if (!function_exists('addVendors')) {
    function addVendors($vendors)
    {
        theme()->addVendors($vendors);
    }
}


if (!function_exists('addVendor')) {
    function addVendor($vendor)
    {
        theme()->addVendor($vendor);
    }
}


if (!function_exists('addJavascriptFile')) {
    function addJavascriptFile($file)
    {
        theme()->addJavascriptFile($file);
    }
}


if (!function_exists('addCssFile')) {
    function addCssFile($file)
    {
        theme()->addCssFile($file);
    }
}


if (!function_exists('getVendors')) {
    function getVendors($type)
    {
        return theme()->getVendors($type);
    }
}


if (!function_exists('getCustomJs')) {
    function getCustomJs()
    {
        return theme()->getCustomJs();
    }
}


if (!function_exists('getCustomCss')) {
    function getCustomCss()
    {
        return theme()->getCustomCss();
    }
}


if (!function_exists('getHtmlAttribute')) {
    function getHtmlAttribute($scope, $attribute)
    {
        return theme()->getHtmlAttribute($scope, $attribute);
    }
}


if (!function_exists('isUrl')) {
    function isUrl($url)
    {
        return filter_var($url, FILTER_VALIDATE_URL);
    }
}


if (!function_exists('image')) {
    function image($path)
    {
        return asset('assets/media/' . $path);
    }
}


if (!function_exists('getIcon')) {
    function getIcon($name, $class = '', $type = '', $tag = 'span')
    {
        return theme()->getIcon($name, $class, $type, $tag);
    }
}
