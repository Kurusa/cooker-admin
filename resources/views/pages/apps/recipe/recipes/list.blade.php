<x-default-layout>
    <style>
        .card-header {
            display: flex;
            justify-content: space-around !important;
        }
    </style>

    @section('title')
        Recipes
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('recipe.recipes.index') }}
    @endsection

    <form method="GET" action="{{ route('recipe.recipes.index') }}" class="mb-4">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search recipes..." value="{{ request('search') }}">

            <select name="source" class="form-select">
                <option value="">All Sources</option>
                @foreach(\App\Models\Source::all() as $source)
                    <option value="{{ $source->title }}" {{ request('source') == $source->title ? 'selected' : '' }}>
                        {{ $source->title }} ({{ $source->recipes()->count() }})
                    </option>
                @endforeach
            </select>

            <select name="filter" class="form-select">
                <option value="">Filter</option>
                <option value="one_step" {{ request('filter') == 'one_step' ? 'selected' : '' }}>
                    One step
                </option>
                <option value="one_ingredient" {{ request('filter') == 'one_ingredient' ? 'selected' : '' }}>
                    One ingredient
                </option>
            </select>

            <button class="btn btn-warning d-none"
                    data-kt-action="reparse-recipes-button"
                    id="reparse-recipes-button"
                    type="button">
                Reparse (<span id="selected-count-reparse">0</span>)
            </button>
            <button class="btn btn-danger d-none"
                    data-kt-action="delete-recipes-button"
                    id="delete-recipes-button"
                    type="button">
                Delete (<span id="selected-count-delete">0</span>)
            </button>
            <button class="btn btn-primary" type="submit">Search</button>
        </div>
    </form>

    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-5 g-xl-9">
        @foreach($recipes as $recipe)
            @include('pages.apps.recipe.recipes.card')
        @endforeach
    </div>

    <div class="d-flex justify-content-center mt-4 mb-4">
        {{ $recipes->appends([
            'search' => request('search'),
            'source' => request('source'),
            'filter' => request('filter'),
        ])->links('pagination::bootstrap-4') }}
    </div>

    @push('scripts')
        <script>
            document.querySelectorAll('[data-kt-action="reparse_recipe"]').forEach(function (element) {
                element.addEventListener('click', function () {
                    let a = $(this);
                    let recipeId = a.attr('data-kt-id');

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
        </script>
    @endpush
</x-default-layout>
