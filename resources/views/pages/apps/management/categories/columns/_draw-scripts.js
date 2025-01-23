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
                Livewire.emit('delete_category', this.getAttribute('data-kt-category-id'));
            }
        });
    });
});

document.querySelectorAll('[data-kt-action="edit_row"]').forEach(function (element) {
    element.addEventListener('click', function () {
        Livewire.emit('editCategory', this.getAttribute('data-kt-category-id'));
    })
});

Livewire.on('success', () => {
    LaravelDataTables['categories-table'].ajax.reload();
});
