<div class="input-group">
    <input type="text" name="search" class="form-control" placeholder="Search recipes..." value="{{ request('search') }}">

    <select name="source" class="form-select">
        <option value="">All Sources</option>
        @foreach(\App\Models\Source::all() as $source)
            <option value="{{ $source->title }}" {{ request('source') == $source->title ? 'selected' : '' }}>
                {{ $source->title }} ({{ $source->recipes()->count() }})
            </option>
        @endforeach
    </select>

    <select name="filter" class="form-select">
        <option value="">Filter</option>
        <option value="one_step" {{ request('filter') == 'one_step' ? 'selected' : '' }}>
            One step
        </option>
        <option value="one_ingredient" {{ request('filter') == 'one_ingredient' ? 'selected' : '' }}>
            One ingredient
        </option>
    </select>

    <button class="btn btn-warning d-none"
            data-kt-action="reparse-recipes-button"
            id="reparse-recipes-button"
            type="button">
        Reparse (<span id="selected-count-reparse">0</span>)
    </button>
    <button class="btn btn-danger d-none"
            data-kt-action="delete-recipes-button"
            id="delete-recipes-button"
            type="button">
        Delete (<span id="selected-count-delete">0</span>)
    </button>
    <button class="btn btn-primary" type="submit">Search</button>
</div>
