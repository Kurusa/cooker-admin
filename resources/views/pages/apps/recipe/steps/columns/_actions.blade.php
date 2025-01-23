@include('partials.datatable-actions._action-button')
<div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
    <div class="menu-item px-3">
        <a href="#" class="menu-link px-3" data-kt-step-id="{{ $step->id }}" data-bs-toggle="modal" data-bs-target="#kt_modal_add_step" data-kt-action="update_row">
            Edit
        </a>
    </div>
    @include('partials.datatable-actions._delete-button', ['model' => 'step', 'id' => $step->id])
</div>
