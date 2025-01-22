<div class="card card-flush mb-6 mb-xl-9">
    <div class="card-body d-flex flex-column">
        @foreach($unparsedUrls as $recipeUrl)
            <div class="d-flex flex-stack position-relative mb-6">
                <div class="position-absolute top-0 start-0 rounded h-100 bg-secondary w-4px"></div>
                <div class="fw-semibold ms-5">
                    <a href="{{ $recipeUrl->url }}"
                       class="fs-5 fw-bold text-gray-900 text-hover-primary"
                       target="_blank"
                       @if($recipeUrl->is_excluded)style="text-decoration: line-through;"@endif
                    >
                        {{ $recipeUrl->url }}
                    </a>
                </div>

                @if(!$recipeUrl->is_excluded)
                    <a href="#"
                       class="btn btn-light-danger bnt-active-light-primary btn-sm excludeSitemap"
                       onclick="excludeRecipeUrl({{ $recipeUrl->id }})"
                    >Exclude</a>
                @endif
            </div>
        @endforeach

        <div class="d-flex justify-content-center mt-4 mb-4">
            {{ $unparsedUrls->appends([
                'search' => request('search'),
                'source' => request('source'),
                'filter' => request('filter'),
            ])->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>

@push('scripts')
    <script>
        function excludeRecipeUrl(recipeUrlId) {
            fetch(`/recipe/recipes/urls/${recipeUrlId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                },
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateRecipeUrlList()
                    } else {
                        Swal.fire({
                            text: data.message || 'Failed to delete sitemap url',
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
        }

        function updateRecipeUrlList() {
            fetch(`/management/sources/{{ $source->id }}/unparsed-urls`)
                .then(response => response.text())
                .then(html => {
                    document.querySelector('#kt_source_view_unparsed_urls_tab').innerHTML = html;
                })
                .catch(error => {
                    console.error('Failed to update recipe URL list', error);
                })
        }
    </script>
@endpush
