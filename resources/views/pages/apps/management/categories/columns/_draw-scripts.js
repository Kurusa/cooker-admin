KTMenu.init();

document.querySelectorAll('[data-kt-action="delete_row"]').forEach(function (element) {
    element.addEventListener('click', function () {
        Swal.fire({
            text: 'Are you sure you want to remove?',
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
                let categoryId = this.getAttribute('data-kt-category-id')

                $.ajax({
                    url: `/management/categories/${categoryId}`,
                    method: 'DELETE',
                    success: function () {
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
                            text: data.message || 'Failed to delete category.',
                            icon: 'error',
                            confirmButtonText: 'OK',
                        });
                    }
                });
            }
        });
    });
});

document.querySelectorAll('[data-kt-action="edit_row"]').forEach(function (element) {
    element.addEventListener('click', function () {
        const categoryId = this.getAttribute('data-kt-category-id');

        fetch(`/management/categories/${categoryId}`)
            .then(response => response.json())
            .then(data => {
                document.querySelector('#kt_modal_edit_category input[name="title"]').value = data.title;
                document.querySelector('#kt_modal_edit_category select[name="parentId"]').value = data.parent_id;

                $('#kt_modal_edit_category select[name="parentId"]').select2().trigger('change');

                if (data.child_categories) {
                    const childSelect = $('#kt_modal_edit_category select[name="childCategories"]');
                    childSelect.val(data.child_categories).trigger('change');
                }

                document.querySelector('input[name="category_id"]').value = categoryId;

                $('#kt_modal_edit_category').modal('show');
            })
            .catch(error => {
                console.error('Error fetching category data:', error);
                alert('Could not fetch category data.');
            });
    });
});

$('#categories-table tbody tr').hover(
    function () {
        if (!$(this).hasClass('shown')) {
            $(this).addClass('hover-highlight');
        }
    },
    function () {
        if (!$(this).hasClass('shown')) {
            $(this).removeClass('hover-highlight');
        }
    }
);

$('#categories-table tbody').off('click', 'tr').on('click', 'tr', function () {
    let tr = $(this);
    let row = window.LaravelDataTables['categories-table'].row(tr);

    if (tr.hasClass('shown')) {
        row.child.hide();
        tr.removeClass('shown');
    } else {
        let categoryId = tr.attr('id');
        $.ajax({
            url: `/management/categories/${categoryId}/details`,
            method: 'GET',
            success: function (response) {
                row.child(response.html).show();
                tr.addClass('shown');
            },
            error: function (xhr) {
                console.error('Error fetching details:', xhr.responseText);
            }
        });
    }
});

Livewire.on('success', () => {
    LaravelDataTables['categories-table'].ajax.reload();
});
