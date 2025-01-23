<x-default-layout>
    <style>
        .hover-highlight {
            background-color: var(--bs-gray-100) !important;
        }
    </style>

    @section('breadcrumbs')
        {{ Breadcrumbs::render('recipe.ingredients.index') }}
    @endsection

    <div class="card">
        @include('pages.apps.recipe.ingredients.partials.actions')

        <div class="card-body py-4">
            <div class="table-responsive">
                {{ $dataTable->table() }}
            </div>
        </div>

        <livewire:recipe.add-ingredient-modal></livewire:ingredient.add-ingredient-modal>
    </div>

    @push('scripts')
        {{ $dataTable->scripts() }}
        <script>
            document.getElementById('ingredientSearchInput').addEventListener('keyup', function () {
                window.LaravelDataTables['ingredients-table'].search(this.value).draw();
            });
            document.addEventListener('livewire:load', function () {
                Livewire.on('success', function () {
                    $('#kt_modal_add_ingredient').modal('hide');
                    window.LaravelDataTables['ingredients-table'].ajax.reload();
                });
            });
        </script>
    @endpush
</x-default-layout>
