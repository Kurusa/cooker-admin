<div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end h-md-50 mb-5 mb-xl-10"
     style="background-color: #F1416C;"
>
    <div class="card-header pt-5">
        <div class="card-title d-flex flex-column">
            <span class="fs-2hx fw-bold text-white me-2 lh-1 ls-n2">{{ $source->parsedUrlsCount() }}</span>
            <span class="text-white opacity-75 pt-1 fw-semibold fs-6">Parsed recipes</span>
        </div>
    </div>
    <div class="card-body d-flex align-items-end pt-0">
        <div class="d-flex align-items-center flex-column mt-3 w-100">
            <div class="d-flex justify-content-between fw-bold fs-6 text-white opacity-75 w-100 mt-auto mb-2">
                <span>{{ $source->pendingUrlsCount() }} Pending</span>
                <span>{{ $source->percentageParsed() }}%</span>
            </div>
            <div class="h-8px mx-3 w-100 bg-white bg-opacity-50 rounded">
                <div class="bg-white rounded h-8px" role="progressbar" style="width: {{ $source->percentageParsed() }}%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        </div>
    </div>
</div>
