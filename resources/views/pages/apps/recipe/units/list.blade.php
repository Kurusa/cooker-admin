<x-default-layout>
    <style>
        .hover-highlight {
            background-color: var(--bs-gray-100) !important;
        }
    </style>

    @section('breadcrumbs')
        {{ Breadcrumbs::render('recipe.units.index') }}
    @endsection

    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <div class="d-flex align-items-center position-relative my-1">
                    {!! getIcon('magnifier', 'fs-3 position-absolute ms-5') !!}
                    <input type="text" data-kt-unit-table-filter="search" class="form-control form-control-solid w-250px ps-13" placeholder="Search unit" id="unitsearchInput"/>
                </div>
            </div>

            <div class="card-toolbar">
                <div class="d-flex justify-content-end" data-kt-unit-table-toolbar="base">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_unit">
                        {!! getIcon('plus', 'fs-2', '', 'i') !!}
                        Add unit
                    </button>
                </div>
            </div>
        </div>

        <div class="card-body py-4">
            <div class="table-responsive">
                {{ $dataTable->table() }}
            </div>
        </div>

        <livewire:recipe.edit-unit-modal></livewire:recipe.edit-unit-modal>
    </div>

    @push('scripts')
        {{ $dataTable->scripts() }}
        <script>
            document.getElementById('unitsearchInput').addEventListener('keyup', function () {
                window.LaravelDataTables['units-table'].search(this.value).draw();
            });
            document.addEventListener('livewire:load', function () {
                Livewire.on('success', function () {
                    $('#kt_modal_add_unit').modal('hide');
                    $('#kt_modal_edit_unit').modal('hide');
                    window.LaravelDataTables['units-table'].ajax.reload();
                });
            });
        </script>
    @endpush
</x-default-layout>
