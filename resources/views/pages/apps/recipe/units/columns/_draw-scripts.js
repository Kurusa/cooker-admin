KTMenu.init();

document.querySelectorAll('[data-kt-action="delete_row"]').forEach(function (element) {
    element.addEventListener('click', function () {
        Swal.fire({
            text: 'Are you sure you want to remove this unit?',
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
                Livewire.emit('delete_unit', this.getAttribute('data-kt-unit-id'));
            }
        });
    });
});

document.querySelectorAll('[data-kt-action="edit_row"]').forEach(function (element) {
    element.addEventListener('click', function () {
        Livewire.emit('editUnit', this.getAttribute('data-kt-unit-id'));
    })
});

$('#units-table tbody tr').hover(
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

$('#units-table tbody').off('click', 'tr').on('click', 'tr', function () {
    let tr = $(this);
    let row = window.LaravelDataTables['units-table'].row(tr);

    if (tr.hasClass('shown')) {
        row.child.hide();
        tr.removeClass('shown');
    } else {
        let unitId = tr.attr('id');
        $.ajax({
            url: `/recipe/units/${unitId}/details`,
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
    LaravelDataTables['units-table'].ajax.reload();
});
