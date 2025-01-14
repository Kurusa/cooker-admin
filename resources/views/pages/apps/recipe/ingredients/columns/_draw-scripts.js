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

$('[data-kt-action="delete_all_ingredients"]').on('click', function () {
    Swal.fire({
        text: 'Are you sure you want to remove all ingredients?',
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
            Livewire.emit('delete_all_ingredients');
        }
    });
})

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

$('#ingredients-table tbody').off('click', 'tr').on('click', 'tr', function () {
    let tr = $(this);
    let row = window.LaravelDataTables['ingredients-table'].row(tr);

    if (tr.hasClass('shown')) {
        row.child.hide();
        tr.removeClass('shown');
    } else {
        let ingredientId = tr.attr('id');
        $.ajax({
            url: `/recipe/ingredients/${ingredientId}/details`,
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

Livewire.on('success', (message) => {
    LaravelDataTables['ingredients-table'].ajax.reload();
});
