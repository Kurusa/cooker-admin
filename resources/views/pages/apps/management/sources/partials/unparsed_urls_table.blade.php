<div id="unparsedUrlsList">
    @foreach($unparsedUrls as $recipeUrl)
        <div class="d-flex flex-stack position-relative mb-6 recipe-url-item">
            <div class="position-absolute top-0 start-0 rounded h-100 bg-secondary w-4px"></div>
            <div class="fw-semibold ms-5">
                <a href="{{ $recipeUrl->url }}"
                   class="fs-5 fw-bold text-gray-900 text-hover-primary"
                   target="_blank"
                   @if($recipeUrl->is_excluded)style="text-decoration: line-through;"@endif
                >
                    {{ $recipeUrl->url }}
                </a>
            </div>

            @if(!$recipeUrl->is_excluded)
                <a href="#"
                   class="btn btn-light-danger bnt-active-light-primary btn-sm excludeSitemap"
                   onclick="excludeRecipeUrl({{ $recipeUrl->id }})"
                >Exclude</a>
            @endif
        </div>
    @endforeach
</div>
