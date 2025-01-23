<div class="card-title">
    <div class="d-flex align-items-center position-relative my-1">
        {!! getIcon('magnifier', 'fs-3 position-absolute ms-5') !!}
        <input type="text"
               data-kt-{{ $model }}-table-filter="search"
               class="form-control form-control-solid w-250px ps-13"
               placeholder="Search {{$model}}"
               id="{{ $model }}SearchInput"
        />
    </div>
</div>
