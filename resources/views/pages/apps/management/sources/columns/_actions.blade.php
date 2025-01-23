@include('partials.datatable-actions._action-button')
<div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
    <div class="menu-item px-3">
        <a href="#" class="menu-link px-3" data-kt-source-id="{{ $source->id }}" data-kt-action="collect_recipe_urls">
            Collect urls
        </a>
    </div>
    @include('partials.datatable-actions._delete-button', ['model' => 'source', 'id' => $source->id])
</div>
