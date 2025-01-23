<x-default-layout>
    <style>
        .hover-highlight {
            background-color: var(--bs-gray-100) !important;
        }
    </style>

    @section('breadcrumbs')
        {{ Breadcrumbs::render('management.categories.index') }}
    @endsection

    <div class="card">
        @include('pages.apps.management.categories.partials.actions')

        <div class="card-body py-4">
            <div class="table-responsive">
                {{ $dataTable->table() }}
            </div>
        </div>

        @include('pages.apps.management.categories.modals.edit-category-modal')
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
