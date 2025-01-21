<div class="col-md-4">
    <div class="card card-flush h-md-100 recipe-card" style="position: relative;">
        <input type="checkbox"
               class="form-check-input recipe-checkbox position-absolute"
               id="checkbox-{{ $recipe->id }}"
               data-recipe-id="{{ $recipe->id }}"
               style="top: 10px; left: 10px; z-index: 10; width: 20px; height: 20px;">

        <div class="card-header" style="padding-left: 40px;">
            <div class="card-title">
                @include('pages.apps.recipe.recipes.partials.title')
                <a href="{{ $recipe->source_url }}" target="_blank" class="d-flex align-items-center text-primary opacity-75-hover fs-6 fw-semibold">
                    <span class="ki-duotone ki-exit-right-corner fs-4 ms-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </span>
                </a>

                <a href="#"
                   class="d-flex align-items-center text-primary opacity-75-hover fs-6 fw-semibold"
                   data-kt-action="reparse_recipe"
                   data-kt-id="{{ $recipe->id }}"
                >
                    <span class="ki-duotone ki-information fs-4 ms-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </span>
                </a>
            </div>
            @include('pages.apps.recipe.recipes.partials.attributes')
        </div>
        <div class="card-body pt-1">
            @if ($recipe->hasImage())
                <div class="mb-4" style="height: 200px; width: 100%; position: relative; overflow: hidden;">
                    <div style="background-image: url('{{ $recipe->image_url }}'); background-size: cover; background-position: center; height: 100%; width: 100%;"></div>
                </div>
            @endif

            <div class="fw-bold text-gray-600 mb-3 d-flex justify-content-between">
                <div class="dropdown">
                    @php($ingredients = $recipe->getDetailedIngredients())
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="ingredientsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        Ingredients ({{ count($ingredients) }})
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="ingredientsDropdown">
                        @foreach($ingredients as $ingredient)
                            <li>
                                <span class="dropdown-item">
                                    {{ $ingredient->ingredient_title }}
                                    @if($ingredient->quantity || $ingredient->unit_title)
                                        - {{ $ingredient->quantity }} {{ $ingredient->unit_title }}
                                    @endif
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="stepsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        Steps ({{ $recipe->steps->count() }})
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
    </div>
</div>
