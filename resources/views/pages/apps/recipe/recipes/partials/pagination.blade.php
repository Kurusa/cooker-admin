<div class="d-flex justify-content-center mt-4 mb-4">
    {{
        $recipes->appends([
            'search' => request('search'),
            'source' => request('source'),
            'filter' => request('filter'),
        ])->links('pagination::bootstrap-4')
    }}
</div>
