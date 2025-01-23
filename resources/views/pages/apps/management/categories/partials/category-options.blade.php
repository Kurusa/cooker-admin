<option></option>
@foreach(\App\Models\Category::all() as $category)
    <option value="{{ $category->id }}">
        @include('pages.apps.management.categories.columns._category', compact('category'))
    </option>
@endforeach
