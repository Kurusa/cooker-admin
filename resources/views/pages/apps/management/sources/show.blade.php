<x-default-layout>
    @section('breadcrumbs')
        {{ Breadcrumbs::render('management.sources.show', $source) }}
    @endsection

    <div class="d-flex flex-column flex-lg-row">
        <div class="flex-column flex-lg-row-auto w-lg-250px w-xl-350px mb-10">
            @include('pages.apps.management.sources.partials.statistic')
        </div>

        <div class="flex-lg-row-fluid ms-lg-15">
            @include('pages.apps.management.sources.partials.actions')

            <div id="myTabContent" class="tab-content">
                <div class="tab-pane fade show active" id="kt_source_view_overview_tab" role="tabpanel">
                    @include('pages.apps.management.sources.partials.sitemap_list')
                    @include('pages.apps.management.sources.partials.debug_parse')
                </div>

                <div class="tab-pane fade show" id="kt_source_view_unparsed_urls_tab" role="tabpanel">
                    @include('pages.apps.management.sources.partials.unparsed_urls_list')
                </div>
            </div>
        </div>
    </div>

    @include('pages/apps/management/sources/modals/_add-sitemap')
</x-default-layout>
