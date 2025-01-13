KTMenu.init();

document.querySelectorAll('[data-kt-action="delete_row"]').forEach(function (element) {
    element.addEventListener('click', function () {
        Swal.fire({
            text: 'Are you sure you want to remove this recipe?',
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
                Livewire.emit('delete_recipe', this.getAttribute('data-kt-recipe-id'));
            }
        });
    });
});

document.querySelectorAll('[data-kt-action="update_row"]').forEach(function (element) {
    element.addEventListener('click', function () {
        Livewire.emit('update_recipe', this.getAttribute('data-kt-recipe-id'));
    });
});

Livewire.on('success', (message) => {
    LaravelDataTables['recipes-table'].ajax.reload();
});
