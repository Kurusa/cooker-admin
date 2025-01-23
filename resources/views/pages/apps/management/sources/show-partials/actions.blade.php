<ul class="nav nav-custom nav-tabs nav-line-tabs nav-line-tabs-2x border-0 fs-4 fw-semibold mb-8" role="tablist">
    <li class="nav-item" role="presentation">
        <a class="nav-link text-active-primary pb-4 active"
           data-bs-toggle="tab"
           href="#kt_source_view_overview_tab"
           aria-selected="true"
           role="tab"
        >
            Overview</a>
    </li>

    <li class="nav-item" role="presentation">
        <a class="nav-link text-active-primary pb-4"
           data-kt-countup-tabs="true" data-bs-toggle="tab"
           href="#kt_source_view_unparsed_urls_tab"
           data-kt-initialized="1"
           aria-selected="false"
           tabindex="-1"
           role="tab"
        >
            Unparsed urls ({{ $source->recipeUrls()->notParsed()->notExcluded()->count() }}
            <span class="w-125px text-gray-500 fw-semibold fs-7">
                + {{ $source->recipeUrls()->notParsed()->isExcluded()->count() }}
            </span>)
        </a>
    </li>

    <li class="nav-item ms-auto">
        <a href="#"
           class="btn btn-primary ps-7"
           data-kt-menu-trigger="click"
           data-kt-menu-attach="parent"
           data-kt-menu-placement="bottom-end"
        >Actions
            <i class="ki-duotone ki-down fs-2 me-0"></i>
        </a>
        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold py-4 w-250px fs-6" data-kt-menu="true" style="">
            <div class="menu-item px-3">
                <a href="#" class="menu-link px-3" data-source-id="{{ $source->id }}" id="parseSource">
                    Parse source
                </a>
            </div>
            <div class="menu-item px-3">
                <a href="#" class="menu-link px-3" data-source-id="{{ $source->id }}" id="collectUrlsButton">
                    Collect urls
                </a>
            </div>
            <div class="menu-item px-3">
                <a href="#"
                   class="menu-link px-3"
                   data-source-id="{{ $source->id }}"
                   data-bs-toggle="modal"
                   data-bs-target="#kt_modal_add_sitemap"
                >
                    Add sitemap
                </a>
            </div>
            <div class="menu-item px-3">
                <a href="#" class="menu-link text-danger px-3" data-kt-source-id="{{ $source->id }}" data-kt-action="delete_row">
                    Delete
                </a>
            </div>
        </div>
    </li>
</ul>

@push('scripts')
    <script>
        document.getElementById('collectUrlsButton').addEventListener('click', function () {
            const sourceId = this.getAttribute('data-source-id');

            fetch(`/management/sources/${sourceId}/collect-urls`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload()
                    } else {
                        Swal.fire({
                            text: data.message || 'Failed to collect URLs.',
                            icon: 'error',
                            confirmButtonText: 'OK',
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        text: 'An error occurred. Please try again.',
                        icon: 'error',
                        confirmButtonText: 'OK',
                    });
                });
        });

        document.getElementById('parseSource').addEventListener('click', function () {
            const sourceId = this.getAttribute('data-source-id');

            fetch(`/management/sources/${sourceId}/parse`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload()
                    } else {
                        Swal.fire({
                            text: data.message || 'Failed to parse source.',
                            icon: 'error',
                            confirmButtonText: 'OK',
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        text: 'An error occurred. Please try again.',
                        icon: 'error',
                        confirmButtonText: 'OK',
                    });
                });
        });
    </script>
@endpush
