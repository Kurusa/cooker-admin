<x-default-layout>
    @section('breadcrumbs')
        {{ Breadcrumbs::render('recipe.steps.index') }}
    @endsection

    <div class="card">
        @include('pages.apps.recipe.steps.partials.actions')

        <div class="card-body py-4">
            <div class="table-responsive">
                {{ $dataTable->table() }}
            </div>
        </div>

        <livewire:recipe.add-step-modal></livewire:recipe.add-step-modal>
    </div>

    @push('scripts')
        {{ $dataTable->scripts() }}
        <script>
            document.getElementById('stepSearchInput').addEventListener('keyup', function () {
                window.LaravelDataTables['steps-table'].search(this.value).draw();
            });
            document.addEventListener('livewire:load', function () {
                Livewire.on('success', function () {
                    $('#kt_modal_add_step').modal('hide');
                    window.LaravelDataTables['steps-table'].ajax.reload();
                });
            });
        </script>
    @endpush
</x-default-layout>
