<div class="modal fade" id="kt_modal_add_sitemap" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Add sitemap url</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-users-modal-action="close">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <form id="kt_modal_add_sitemap_form" class="form" action="#">
                    <div class="fv-row mb-7">
                        <label class="required fs-6 fw-semibold form-label mb-2">Url</label>
                        <input type="text" class="form-control form-control-solid" name="url" value="" id="url"/>
                    </div>
                    <div class="text-center pt-15">
                        <button type="reset" class="btn btn-light me-3" data-kt-users-modal-action="cancel">Discard</button>
                        <button type="submit" class="btn btn-primary" id="addSitemap" data-source-id="{{ $source->id }}">
                            <span class="indicator-label">Submit</span>
                            <span class="indicator-progress">Please wait...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.getElementById('addSitemap').addEventListener('click', function (e) {
            e.preventDefault()

            let sourceId = this.getAttribute('data-source-id');

            fetch(`/management/sources/${sourceId}/sitemap`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    url: document.getElementById('url').value,
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload()
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
