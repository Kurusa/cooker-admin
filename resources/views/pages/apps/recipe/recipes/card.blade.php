<div class="col-md-4">
    <div class="card card-flush h-md-100">
        <div class="card-header">
            <div class="card-title">
                @include('pages.apps.recipe.recipes.partials.title')
                <a href="{{ $recipe->source_url }}" target="_blank" class="d-flex align-items-center text-primary opacity-75-hover fs-6 fw-semibold">
                    <span class="ki-duotone ki-exit-right-corner fs-4 ms-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </span>
                </a>
            </div>
            <div class="w-100"></div>
            <div class="d-flex align-items-center flex-wrap d-grid gap-2">
                <div class="d-flex align-items-center me-5 me-xl-13">
                    <div class="symbol symbol-30px symbol-circle me-3">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                    <div class="m-0">
                        <a href="#" class="fw-bold text-gray-800 text-hover-primary fs-7">{{ $recipe->time }}</a>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-30px symbol-circle me-3">
                        <i class="fa-solid fa-bowl-food"></i>
                    </div>
                    <div class="m-0">
                        <a href="#" class="fw-bold text-gray-800 text-hover-primary fs-7">{{ $recipe->portions }}</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body pt-1">
            @if ($recipe->hasImage())
                <div class="mb-4" style="height: 200px; width: 100%; position: relative; overflow: hidden;">
                    <div style="background-image: url('{{ $recipe->image_url }}'); background-size: cover; background-position: center; height: 100%; width: 100%;"></div>
                </div>
            @endif

            <div class="fw-bold text-gray-600 mb-3 d-flex justify-content-between">
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="ingredientsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        Ingredients
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="ingredientsDropdown">
                        @foreach($recipe->getDetailedIngredients() as $ingredient)
                            <li>
                                <span class="dropdown-item">
                                    {{ $ingredient->ingredient_title }} - {{ $ingredient->quantity }} {{ $ingredient->unit_title }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="stepsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        Steps
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="stepsDropdown">
                        @foreach($recipe->steps as $step)
                            <li>
                                <span class="dropdown-item">
                                    {{ $loop->iteration }}. {{ $step->description }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <div class="card-footer flex-wrap pt-0">
            <a href="" class="btn btn-light btn-active-primary my-1 me-2">View Recipe</a>
            <button type="button" class="btn btn-light btn-active-light-primary my-1" data-role-id="{{ $recipe->id }}" data-bs-toggle="modal" data-bs-target="#kt_modal_update_role">Edit Recipe</button>
        </div>
    </div>
</div>
