<x-default-layout>
    @section('title')
        Sources
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('management.sources.show', $source) }}
    @endsection

    <div class="d-flex flex-column flex-lg-row">
        <div class="flex-column flex-lg-row-auto w-lg-250px w-xl-350px mb-10">
            <div class="card mb-5 mb-xl-8">
                <div class="card-body">
                    <div class="d-flex flex-center flex-column py-5">
                        <div class="fw-bold mb-3">
                            @include('pages.apps.management.sources.columns._recipes')
                        </div>
                        <div class="d-flex flex-wrap flex-center">
                            <div class="border border-gray-300 border-dashed rounded py-3 px-3 mb-3">
                                <div class="fs-4 fw-bold text-gray-700">
                                    <span class="w-75px">{{ $source->recipeUrls->count() }}</span>
                                </div>
                                <div class="fw-semibold text-muted">Total</div>
                            </div>
                            <div class="border border-gray-300 border-dashed rounded py-3 px-3 mx-4 mb-3">
                                <div class="fs-4 fw-bold text-gray-700">
                                    <span class="w-50px">{{ $source->recipes->count() }}</span>
                                </div>
                                <div class="fw-semibold text-muted">Parsed</div>
                            </div>
                            <div class="border border-gray-300 border-dashed rounded py-3 px-3 mb-3">
                                <div class="fs-4 fw-bold text-gray-700">
                                    <span class="w-50px">{{ $source->recipeUrls->count() - $source->recipes->count() }}</span>
                                </div>
                                <div class="fw-semibold text-muted">Left</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex-lg-row-fluid ms-lg-15">
            <ul class="nav nav-custom nav-tabs nav-line-tabs nav-line-tabs-2x border-0 fs-4 fw-semibold mb-8" role="tablist">
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
                            <a href="#" class="menu-link px-3" data-source-id="{{ $source->id }}" id="collectUrlsButton">
                                Collect urls
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
            <div class="tab-content">
                <div class="tab-pane fade show active" id="kt_source_view_overview_tab" role="tabpanel">
                    <div class="card card-flush mb-6 mb-xl-9">
                        <div class="card-header mt-6">
                            <div class="card-title flex-column">
                                <h2 class="mb-1">Sitemaps</h2>
                            </div>
                            <div class="card-toolbar">
                                <button
                                    type="button"
                                    class="btn btn-light-primary btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#kt_modal_add_sitemap"
                                >
                                    Add sitemap
                                </button>
                            </div>
                        </div>
                        <div class="card-body d-flex flex-column">
                            @foreach($source->sitemaps as $sitemap)
                                <div class="d-flex align-items-center position-relative mb-7">
                                    <div class="position-absolute top-0 start-0 rounded h-100 bg-secondary w-4px"></div>
                                    <div class="fw-semibold ms-5">
                                        <a href="{{ $sitemap->url }}" class="fs-5 fw-bold text-gray-900 text-hover-primary">
                                            {{ $sitemap->url }}
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="card card-flush mb-6 mb-xl-9">
                        <div class="card-header mt-6">
                            <div class="card-title flex-column">
                                <h2 class="mb-1">Debug</h2>
                            </div>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <div class="mb-4">
                                <label for="recipeUrl" class="form-label">Recipe URL</label>
                                <input type="text" id="recipeUrl" class="form-control" placeholder="Enter recipe URL"/>
                            </div>

                            <div id="recipeDataContainer" style="display:none;">
                                <h4 class="mt-4">Recipe Data:</h4>
                                <div id="recipeData"></div>
                            </div>

                            <button id="fetchRecipeData"
                                    class="btn btn-primary mt-4"
                                    data-source-id="{{ $source->id }}"
                            >Fetch recipe data</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('pages/apps/management/sources/modals/_add-sitemap')
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
                            Swal.fire({
                                text: 'URLs collected successfully!',
                                icon: 'success',
                                confirmButtonText: 'OK',
                            });
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

            document.getElementById('fetchRecipeData').addEventListener('click', function () {
                fetch(`/recipe/recipes/parse/debug`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        url: document.getElementById('recipeUrl').value,
                        source_id: this.getAttribute('data-source-id'),
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        console.log(data)
                        if (data.success) {
                            const recipeDataContainer = document.getElementById('recipeDataContainer');
                            const recipeData = document.getElementById('recipeData');

                            recipeDataContainer.style.display = 'block';
                            recipeData.innerHTML = data.view
                        } else {
                            Swal.fire({
                                text: data.message || 'Failed to fetch recipe data.',
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
</x-default-layout>
