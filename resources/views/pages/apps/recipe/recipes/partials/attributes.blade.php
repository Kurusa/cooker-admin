@if($recipe->time || $recipe->portions)
    <div class="w-100"></div>

    <div class="d-flex align-items-center flex-wrap d-grid gap-2">
        @if($recipe->time)
            <div class="d-flex align-items-center me-5 me-xl-13">
                <div class="symbol symbol-30px symbol-circle me-3">
                    <i class="fa-solid fa-clock"></i>
                </div>
                <div class="m-0">
                    <a href="#" class="fw-bold text-gray-800 text-hover-primary fs-7">{{ $recipe->time }}</a>
                </div>
            </div>
        @endif
        @if($recipe->portions)
            <div class="d-flex align-items-center">
                <div class="symbol symbol-30px symbol-circle me-3">
                    <i class="fa-solid fa-bowl-food"></i>
                </div>
                <div class="m-0">
                    <a href="#" class="fw-bold text-gray-800 text-hover-primary fs-7">{{ $recipe->portions }}</a>
                </div>
            </div>
        @endif
    </div>
@endif
