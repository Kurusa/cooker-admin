<x-default-layout>

    @section('title')
        Categories
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('management.categories.index') }}
    @endsection

    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <div class="d-flex align-items-center position-relative my-1">
                    {!! getIcon('magnifier', 'fs-3 position-absolute ms-5') !!}
                    <input type="text" data-kt-source-table-filter="search" class="form-control form-control-solid w-250px ps-13" placeholder="Search category" id="mySearchInput"/>
                </div>
            </div>

            <div class="card-toolbar">
                <div class="d-flex justify-content-end" data-kt-source-table-toolbar="base">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_category">
                        {!! getIcon('plus', 'fs-2', '', 'i') !!}
                        Add category
                    </button>
                </div>

                <livewire:category.add-category-modal></livewire:category.add-category-modal>
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
            document.getElementById('mySearchInput').addEventListener('keyup', function () {
                window.LaravelDataTables['categories-table'].search(this.value).draw();
            });
            document.addEventListener('livewire:load', function () {
                Livewire.on('success', function () {
                    $('#kt_modal_add_category').modal('hide');
                    window.LaravelDataTables['categories-table'].ajax.reload();
                });
            });
        </script>
    @endpush

</x-default-layout>
