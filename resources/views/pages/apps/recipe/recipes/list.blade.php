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

            <button class="btn btn-warning d-none"
                    data-kt-action="reparse-recipes-button"
                    id="reparse-recipes-button"
                    type="button">
                Reparse
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
        {{ $recipes->appends(['search' => request('search')])->links('pagination::bootstrap-4') }}
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
                            $.ajax({
                                url: `/recipe/recipes/${recipeId}/reparse`,
                                method: 'GET',
                                success: function (response) {
                                    location.reload()
                                },
                                error: function (xhr) {
                                    console.error('Error fetching details:', xhr.responseText);
                                }
                            });
                        }
                    });
                });
            });

            document.addEventListener('DOMContentLoaded', function () {
                const recipeCards = document.querySelectorAll('.recipe-card');

                recipeCards.forEach(card => {
                    card.addEventListener('click', function (event) {
                        if (event.target.tagName === 'INPUT' && event.target.type === 'checkbox') {
                            return;
                        }

                        const checkbox = card.querySelector('.recipe-checkbox');

                        if (checkbox) {
                            checkbox.checked = !checkbox.checked;

                            updateReparseButtonVisibility();
                        }
                    });
                });

                const updateReparseButtonVisibility = () => {
                    const selectedCheckboxes = document.querySelectorAll('.recipe-checkbox:checked');
                    const reparseButton = document.getElementById('reparse-recipes-button');

                    if (selectedCheckboxes.length > 0) {
                        reparseButton.classList.remove('d-none');
                    } else {
                        reparseButton.classList.add('d-none');
                    }
                };

                updateReparseButtonVisibility();
            });

            document.getElementById('reparse-recipes-button').addEventListener('click', function (event) {
                event.preventDefault();

                const selectedIds = Array.from(document.querySelectorAll('.recipe-checkbox:checked'))
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
                        fetch('/recipe/recipes/reparse', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({recipe_ids: selectedIds})
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        text: data.message,
                                        icon: 'success',
                                        buttonsStyling: false,
                                        confirmButtonText: 'OK',
                                        customClass: {
                                            confirmButton: 'btn btn-primary',
                                        }
                                    });
                                    location.reload()
                                } else {
                                    console.error('Error:', data);
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                            });
                    }
                });
            });
        </script>
    @endpush
</x-default-layout>
