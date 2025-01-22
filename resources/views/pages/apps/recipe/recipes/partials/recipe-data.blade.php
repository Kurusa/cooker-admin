<div class="table-responsive">
    <table class="table align-middle table-row-dashed gy-5" id="kt_table_users_login_session">
        <tbody class="fs-6 fw-semibold text-gray-600">
        <tr>
            <td>Title</td>
            <td>{{ $recipe['title'] }}</td>
        </tr>
        <tr>
            <td>Image</td>
            <td>{{ $recipe['image'] }}</td>
        </tr>
        <tr>
            <td>Category</td>
            <td>{{ $recipe['category'] }}</td>
        </tr>
        <tr>
            <td>Complexity</td>
            <td>{{ $recipe['complexity'] }}</td>
        </tr>
        <tr>
            <td>Cooking</td>
            <td>{{ $recipe['cookingTime'] }}</td>
        </tr>
        <tr>
            <td>Portions</td>
            <td>{{ $recipe['portions'] }}</td>
        </tr>
        <tr>
            <td>Ingredients</td>
            <td>
                @foreach($recipe['ingredients'] as $ingredient)
                    - {!! is_array($ingredient) ? $ingredient['title'] : $ingredient !!} <br>
                @endforeach
            </td>
        </tr>
        <tr>
            <td>Steps</td>
            <td>
                @foreach($recipe['steps'] as $step)
                    - {!! is_array($step) ? $step['description'] : $step !!} <br>
                @endforeach
            </td>
        </tr>
        </tbody>
    </table>
</div>
