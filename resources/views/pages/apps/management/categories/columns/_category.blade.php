@if ($category->parent)
    <span class="text-gray-500 mt-1 fw-semibold fs-6">{{ $category->parent->title }} -> </span>
@endif
{{ $category->title }} ({{ $category->recipes->count()}})
