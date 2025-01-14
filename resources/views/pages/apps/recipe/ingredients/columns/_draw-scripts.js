KTMenu.init();

document.querySelectorAll('[data-kt-action="delete_row"]').forEach(function (element) {
    element.addEventListener('click', function () {
        Swal.fire({
            text: 'Are you sure you want to remove this ingredient?',
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
                Livewire.emit('delete_ingredient', this.getAttribute('data-kt-ingredient-id'));
            }
        });
    });
});

document.querySelectorAll('[data-kt-action="update_row"]').forEach(function (element) {
    element.addEventListener('click', function () {
        Livewire.emit('update_ingredient', this.getAttribute('data-kt-ingredient-id'));
    });
});

$('#ingredients-table tbody tr').hover(
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

$('#ingredients-table tbody').on('click', 'tr', function () {
    let tr = $(this);
    let row = window.LaravelDataTables['ingredients-table'].row(tr);

    if (row.child.isShown()) {
        row.child.hide();
        tr.removeClass('shown');
    } else {
        let ingredientId = tr.attr('id');

        tr.addClass('hover-highlight');

        row.child($('#ingredient-details-container').html()).show();
        Livewire.emit('show_ingredient_details', ingredientId);
        tr.addClass('shown');
    }
});

Livewire.on('success', (message) => {
    LaravelDataTables['ingredients-table'].ajax.reload();
});
