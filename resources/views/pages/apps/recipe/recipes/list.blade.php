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
        </script>
    @endpush
</x-default-layout>
