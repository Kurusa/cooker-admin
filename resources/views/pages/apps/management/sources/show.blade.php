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
                        <div class="fw-bold mb-3">Recipes</div>
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
            <div class="tab-content" id="myTabContent">
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
                                    <i class="ki-duotone ki-add-files fs-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>Add sitemap
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
                </div>
            </div>
        </div>
    </div>

    @include('pages/apps/management/sources/modals/_add-source')
</x-default-layout>
