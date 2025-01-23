<div>
    <div class="separator separator-dashed my-5"></div>
    @if ($unit->recipeIngredients->isNotEmpty())
        <table class="table table-sm">
            <thead>
            <tr>
                <th>Name</th>
                <th>Quantity</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($unit->recipeIngredients as $recipeIngredient)
                <tr>
                    <td>
                        {{ $recipeIngredient->ingredientUnit->ingredient->title }}
                    </td>
                    <td>
                        {{ $recipeIngredient->quantity }}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
</div>
