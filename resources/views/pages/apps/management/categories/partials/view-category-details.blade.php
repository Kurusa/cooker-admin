<div>
    <div class="separator separator-dashed my-5"></div>
    @if ($category->children->isNotEmpty())
        <table class="table table-sm">
            <thead>
            <tr>
                <th>Name</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($category->children as $category)
                <tr>
                    <td>
                        @include('pages.apps.management.categories.columns._category', compact('category'))
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
</div>
