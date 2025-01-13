<x-default-layout>
    @section('title')
        Ingredients
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('recipe.ingredients.index') }}
    @endsection

    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <div class="d-flex align-items-center position-relative my-1">
                    {!! getIcon('magnifier', 'fs-3 position-absolute ms-5') !!}
                    <input type="text" data-kt-ingredient-table-filter="search" class="form-control form-control-solid w-250px ps-13" placeholder="Search ingredient" id="ingredientSearchInput"/>
                </div>
            </div>

            <div class="card-toolbar">
                <div class="d-flex justify-content-end" data-kt-ingredient-table-toolbar="base">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_ingredient">
                        {!! getIcon('plus', 'fs-2', '', 'i') !!}
                        Add ingredient
                    </button>
                </div>

                <livewire:recipe.add-ingredient-modal></livewire:ingredient.add-ingredient-modal>
            </div>
        </div>

        <div class="card-body py-4">
            <div class="table-responsive">
                {{ $dataTable->table() }}
            </div>
        </div>
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
