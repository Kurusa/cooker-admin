@include('partials.datatable-actions._action-button')
<div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
    @include('partials.datatable-actions._delete-button', ['model' => 'source', 'id' => $source->id])
</div>
