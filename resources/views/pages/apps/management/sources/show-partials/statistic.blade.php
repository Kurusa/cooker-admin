<div class="d-flex flex-wrap flex-center">
    <div class="border border-gray-300 border-dashed rounded py-3 px-3 mx-4 mb-3">
        <div class="fs-4 fw-bold text-gray-700">
            <span class="w-50px">{{ $source->recipes->count() }}</span>
        </div>
        <div class="fw-semibold text-muted">Parsed</div>
    </div>
    <div class="border border-gray-300 border-dashed rounded py-3 px-3 mx-4 mb-3">
        <div class="fs-4 fw-bold text-gray-700">
            <span class="w-50px">
                {{ $source->recipeUrls()->notParsed()->where('is_excluded', 0)->count() }}
                <span class="w-125px text-gray-500 fw-semibold fs-7">
                    + {{ $source->recipeUrls()->notParsed()->where('is_excluded', 1)->count() }}
                </span>
            </span>
        </div>
        <div class="fw-semibold text-muted">To parse</div>
    </div>
    <div class="border border-gray-300 border-dashed rounded py-3 px-3 mx-4 mb-3">
        <div class="fs-4 fw-bold text-gray-700">
            <span class="w-75px">{{ $source->recipeUrls->count() }}</span>
        </div>
        <div class="fw-semibold text-muted">Total</div>
    </div>
</div>
