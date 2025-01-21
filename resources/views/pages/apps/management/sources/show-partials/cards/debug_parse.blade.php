<div class="card card-flush mb-6 mb-xl-9">
    <div class="card-header mt-6">
        <div class="card-title flex-column">
            <h2 class="mb-1">Debug</h2>
        </div>
    </div>
    <div class="card-body d-flex flex-column">
        <div class="mb-4">
            <label for="recipeUrl" class="form-label">Recipe URL</label>
            <input type="text" id="recipeUrl" class="form-control" placeholder="Enter recipe URL"/>
        </div>

        <div id="recipeDataContainer" style="display:none;">
            <h4 class="mt-4">Recipe Data:</h4>
            <div id="recipeData"></div>
        </div>

        <button id="fetchRecipeData"
                class="btn btn-primary mt-4"
                data-source-id="{{ $source->id }}"
        >Fetch recipe data
        </button>
    </div>
</div>

@push('scripts')
    <script>
        document.getElementById('fetchRecipeData').addEventListener('click', function () {
            fetch(`/recipe/recipes/parse/debug`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    url: document.getElementById('recipeUrl').value,
                    source_id: this.getAttribute('data-source-id'),
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const recipeDataContainer = document.getElementById('recipeDataContainer');
                        const recipeData = document.getElementById('recipeData');

                        recipeDataContainer.style.display = 'block';
                        recipeData.innerHTML = data.view
                    } else {
                        Swal.fire({
                            text: data.message || 'Failed to fetch recipe data.',
                            icon: 'error',
                            confirmButtonText: 'OK',
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        text: 'An error occurred. Please try again.',
                        icon: 'error',
                        confirmButtonText: 'OK',
                    });
                });
        });
    </script>
@endpush
