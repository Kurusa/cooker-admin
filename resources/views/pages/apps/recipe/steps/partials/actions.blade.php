<div class="card-header border-0 pt-6">
    @include('partials.datatable-actions._search-input', ['model' => 'step'])

    <div class="card-toolbar">
        <div class="d-flex justify-content-end" data-kt-step-table-toolbar="base">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_step">
                {!! getIcon('plus', 'fs-2', '', 'i') !!}
                Add step
            </button>
        </div>
    </div>
</div>
