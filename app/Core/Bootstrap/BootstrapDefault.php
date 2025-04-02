<?php

namespace App\Core\Bootstrap;

class BootstrapDefault
{
    public function init()
    {
        $this->initDarkSidebarLayout();

        $this->initAssets();
    }

    public function initAssets(): void
    {
        # Include global vendors
        addVendors(['datatables']);

        # Include global javascript files
        addJavascriptFile('assets/js/custom/widgets.js');
    }

    public function initDarkSidebarLayout(): void
    {
        addHtmlAttribute('body', 'data-kt-app-layout', 'dark-sidebar');
        addHtmlAttribute('body', 'data-kt-app-header-fixed', 'true');
        addHtmlAttribute('body', 'data-kt-app-sidebar-enabled', 'true');
        addHtmlAttribute('body', 'data-kt-app-sidebar-fixed', 'true');
        addHtmlAttribute('body', 'data-kt-app-sidebar-hoverable', 'true');
        addHtmlAttribute('body', 'data-kt-app-sidebar-push-header', 'true');
        addHtmlAttribute('body', 'data-kt-app-sidebar-push-toolbar', 'true');
        addHtmlAttribute('body', 'data-kt-app-sidebar-push-footer', 'true');
        addHtmlAttribute('body', 'data-kt-app-toolbar-enabled', 'true');

        addHtmlClass('body', 'app-default');
    }
}
