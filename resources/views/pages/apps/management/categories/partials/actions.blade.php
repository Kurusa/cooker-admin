<div class="card-header border-0 pt-6">
    @include('partials.datatable-actions._search-input', ['model' => 'category'])

    <div class="card-toolbar">
        <div class="d-flex justify-content-end" data-kt-source-table-toolbar="base">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_category">
                {!! getIcon('plus', 'fs-2', '', 'i') !!}
                Add category
            </button>
        </div>
    </div>
</div>
