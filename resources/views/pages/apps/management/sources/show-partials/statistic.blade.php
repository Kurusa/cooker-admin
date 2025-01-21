<div class="d-flex flex-wrap flex-center">
    <div class="border border-gray-300 border-dashed rounded py-3 px-3 mb-3">
        <div class="fs-4 fw-bold text-gray-700">
            <span class="w-75px">{{ $source->recipeUrls->count() }}</span>
        </div>
        <div class="fw-semibold text-muted">Total</div>
    </div>
    <div class="border border-gray-300 border-dashed rounded py-3 px-3 mx-4 mb-3">
        <div class="fs-4 fw-bold text-gray-700">
            <span class="w-50px">{{ $source->recipes->count() }}</span>
        </div>
        <div class="fw-semibold text-muted">Parsed</div>
    </div>
    <div class="border border-gray-300 border-dashed rounded py-3 px-3 mb-3">
        <div class="fs-4 fw-bold text-gray-700">
            <span class="w-50px">{{ $source->recipeUrls->count() - $source->recipes->count() }}</span>
        </div>
        <div class="fw-semibold text-muted">Left</div>
    </div>
</div>
