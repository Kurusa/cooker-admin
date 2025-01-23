<div class="card-header border-0 pt-6">
    @include('partials.datatable-actions._search-input', ['model' => 'ingredient'])

    <div class="card-toolbar">
        <div class="d-flex justify-content-end" data-kt-ingredient-table-toolbar="base">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_ingredient">
                {!! getIcon('plus', 'fs-2', '', 'i') !!}
                Add ingredient
            </button>
        </div>
    </div>
</div>
