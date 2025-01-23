<div class="modal fade" id="kt_modal_edit_category" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-body px-5 my-7">
                <form id="kt_modal_edit_category_form">
                    <div class="d-flex flex-column scroll-y px-5 px-lg-10" id="kt_modal_edit_category_scroll">
                        <input hidden name="category_id" value="">
                        <div class="fv-row mb-7">
                            <label class="form-label fs-6 fw-semibold">Title</label>
                            <input name="title"
                                   class="form-control form-control-solid"
                                   placeholder="Category title"
                                   rows="15"
                            >
                        </div>

                        <div class="fv-row mb-7">
                            <label class="form-label fs-6 fw-semibold">Parent category:</label>
                            <select
                                name="parentId"
                                aria-label="Select parent category"
                                data-control="select2"
                                data-placeholder="Select parent category"
                                class="form-select form-select-solid"
                                data-dropdown-parent="#kt_modal_edit_category"
                            >
                                @include('pages.apps.management.categories.partials.category-options')
                            </select>
                        </div>

                        <div class="fv-row mb-7">
                            <label class="form-label fs-6 fw-semibold">Child categories:</label>
                            <select
                                name="childCategories"
                                aria-label="Select child categories"
                                data-control="select2"
                                data-placeholder="Select child categories"
                                class="form-select form-select-solid multi-select"
                                data-dropdown-parent="#kt_modal_edit_category"
                                multiple="multiple"
                            >
                                @include('pages.apps.management.categories.partials.category-options')
                            </select>
                        </div>

                        <div class="fv-row mb-7">
                            <label class="form-label fs-6 fw-semibold">Merge with:</label>
                            <select
                                name="mergeCategories"
                                aria-label="Select categories to merge with"
                                data-control="select2"
                                data-placeholder="Select categories to merge with"
                                class="form-select form-select-solid multi-select"
                                data-dropdown-parent="#kt_modal_edit_category"
                                multiple="multiple"
                            >
                                @include('pages.apps.management.categories.partials.category-options')
                            </select>
                        </div>
                    </div>
                    <div class="text-center pt-15">
                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Discard</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $('#kt_modal_edit_category').on('hidden.bs.modal', function (e) {
            $(this).find('form').trigger('reset');
            $("[name='mergeCategories']").val(null).trigger('change')
            $("[name='childCategories']").val(null).trigger('change')
            $("[name='parentId']").val(null).trigger('change')
        })

        document.querySelector('#kt_modal_edit_category_form').addEventListener('submit', function (e) {
            e.preventDefault();

            const categoryId = document.querySelector('input[name="category_id"]').value;

            $.ajax({
                url: `/management/categories/${categoryId}`,
                method: 'PUT',
                data: {
                    title: $('[name="title"]').val(),
                    parentId: $('[name="parentId"]').val(),
                    mergeCategories: $('[name="mergeCategories"]').val(),
                    childCategories: $('[name="childCategories"]').val()
                },
                success: function () {
                    $('#kt_modal_edit_category').modal('hide');

                    Swal.fire({
                        text: 'Success',
                        icon: 'success',
                        buttonsStyling: false,
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn btn-primary',
                        }
                    }).then(() => {
                        window.LaravelDataTables['categories-table'].ajax.reload();
                    });
                },
                error: function () {
                    Swal.fire({
                        text: data.message || 'Failed to update category.',
                        icon: 'error',
                        confirmButtonText: 'OK',
                    });
                }
            });
        });
    </script>
@endpush
