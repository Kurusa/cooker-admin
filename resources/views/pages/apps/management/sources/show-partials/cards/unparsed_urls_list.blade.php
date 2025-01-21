<div class="card card-flush mb-6 mb-xl-9">
    <div class="card-body d-flex flex-column">
        @foreach($source->recipeUrls()->where('is_parsed', 0)->get() as $recipeUrl)
            <div class="d-flex flex-stack position-relative mb-6">
                <div class="position-absolute top-0 start-0 rounded h-100 bg-secondary w-4px"></div>
                <div class="fw-semibold ms-5">
                    <a href="{{ $recipeUrl->url }}" class="fs-5 fw-bold text-gray-900 text-hover-primary">
                        {{ $recipeUrl->url }}
                    </a>
                </div>

                <a href="#"
                   class="btn btn-light-danger bnt-active-light-primary btn-sm deleteSitemap"
                >Delete</a>
            </div>
        @endforeach
    </div>
</div>

@push('scripts')
    <script>
        function deleteSitemapUrl(sourceId, sitemapId) {
            Swal.fire({
                text: 'Are you sure you want to remove this sitemap url?',
                icon: 'warning',
                buttonsStyling: false,
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'No',
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-secondary',
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/management/sources/${sourceId}/sitemap/${sitemapId}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                location.reload()
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
            });
        }
    </script>
@endpush
