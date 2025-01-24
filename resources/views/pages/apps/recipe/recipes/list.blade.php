<x-default-layout>
    <style>
        .card-header {
            display: flex;
            justify-content: space-around !important;
        }
    </style>

    @section('breadcrumbs')
        {{ Breadcrumbs::render('recipe.recipes.index') }}
    @endsection

    <form method="GET" action="{{ route('recipe.recipes.index') }}" class="mb-4">
        @include('pages.apps.recipe.recipes.partials.filters')
    </form>

    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-5 g-xl-9">
        @foreach($recipes as $recipe)
            @include('pages.apps.recipe.recipes.card')
        @endforeach
    </div>

    @include('pages.apps.recipe.recipes.partials.pagination')

    @push('scripts')
        <script>
            document.querySelectorAll('[data-kt-action="reparse_recipe"]').forEach(function (element) {
                element.addEventListener('click', function () {
                    let recipeId = $(this).attr('data-kt-id');

                    Swal.fire({
                        text: 'Are you sure you want to reparse this recipe?',
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
                            reparseSelectedRecipes({recipeId})
                        }
                    });
                });
            });

            document.addEventListener('DOMContentLoaded', function () {
                const recipeCards = document.querySelectorAll('.recipe-card');
                const checkboxes = document.querySelectorAll('.recipe-checkbox');
                const reparseButton = document.getElementById('reparse-recipes-button');
                const deleteButton = document.getElementById('delete-recipes-button');
                const selectedCountReparse = document.getElementById('selected-count-reparse');
                const selectedCountDelete = document.getElementById('selected-count-delete');

                const updateReparseButtonVisibility = () => {
                    const selected = Array.from(checkboxes).filter(checkbox => checkbox.checked);
                    selectedCountReparse.textContent = selected.length;

                    if (selected.length > 0) {
                        reparseButton.classList.remove('d-none');
                    } else {
                        reparseButton.classList.add('d-none');
                    }
                };

                const updateDeleteButtonVisibility = () => {
                    const selected = Array.from(checkboxes).filter(checkbox => checkbox.checked);
                    selectedCountDelete.textContent = selected.length;

                    if (selected.length > 0) {
                        deleteButton.classList.remove('d-none');
                    } else {
                        deleteButton.classList.add('d-none');
                    }
                };

                recipeCards.forEach(card => {
                    card.addEventListener('click', function (event) {
                        if (event.target.tagName === 'INPUT' && event.target.type === 'checkbox') {
                            return;
                        }

                        const checkbox = card.querySelector('.recipe-checkbox');

                        if (checkbox) {
                            checkbox.checked = !checkbox.checked;

                            updateReparseButtonVisibility();
                            updateDeleteButtonVisibility();
                        }
                    });
                });

                checkboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', updateReparseButtonVisibility);
                    checkbox.addEventListener('change', updateDeleteButtonVisibility);
                });

                reparseButton.addEventListener('click', function (event) {
                    event.preventDefault();

                    const selectedIds = Array.from(checkboxes)
                        .filter(checkbox => checkbox.checked)
                        .map(checkbox => checkbox.getAttribute('data-recipe-id'));

                    if (selectedIds.length === 0) {
                        Swal.fire({
                            text: 'Please select at least one recipe to reparse.',
                            icon: 'warning',
                            buttonsStyling: false,
                            confirmButtonText: 'OK',
                            customClass: {
                                confirmButton: 'btn btn-primary',
                            }
                        });
                        return;
                    }

                    Swal.fire({
                        text: 'Are you sure you want to reparse the selected recipes?',
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
                            reparseSelectedRecipes(selectedIds)
                        }
                    });
                });
                deleteButton.addEventListener('click', function (event) {
                    event.preventDefault();

                    const selectedIds = Array.from(checkboxes)
                        .filter(checkbox => checkbox.checked)
                        .map(checkbox => checkbox.getAttribute('data-recipe-id'));

                    if (selectedIds.length === 0) {
                        Swal.fire({
                            text: 'Please select at least one recipe to delete.',
                            icon: 'warning',
                            buttonsStyling: false,
                            confirmButtonText: 'OK',
                            customClass: {
                                confirmButton: 'btn btn-primary',
                            }
                        });
                        return;
                    }

                    Swal.fire({
                        text: 'Are you sure you want to delete the selected recipes?',
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
                            $.ajax({
                                url: '/recipe/recipes',
                                method: 'DELETE',
                                data: {recipe_ids: selectedIds},
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
                                        location.reload();
                                    });
                                },
                                error: function (xhr) {
                                    console.error('Error:', xhr.responseText);
                                }
                            });
                        }
                    });
                });

                updateReparseButtonVisibility();
                updateDeleteButtonVisibility();
            });

            function reparseSelectedRecipes(selectedIds) {
                $.ajax({
                    url: '/recipe/recipes/parse',
                    method: 'POST',
                    data: {recipe_ids: selectedIds},
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
                            location.reload();
                        });
                    },
                    error: function (xhr) {
                        console.error('Error:', xhr.responseText);
                    }
                });
            }

            document.getElementById('deleteAllRecipes').addEventListener('click', function () {
                $.ajax({
                    url: '/recipe/recipes/all',
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
                            location.reload();
                        });
                    },
                    error: function (xhr) {
                        console.error('Error:', xhr.responseText);
                    }
                });
            })
        </script>
    @endpush
</x-default-layout>
