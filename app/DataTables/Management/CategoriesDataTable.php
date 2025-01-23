<?php

namespace App\DataTables\Management;

use App\Models\Category;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class CategoriesDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->rawColumns(['id', 'title', 'children_count', 'recipes_count'])
            ->editColumn('title', function (Category $category) {
                return view('pages.apps.management.categories.columns._category', compact('category'));
            })
            ->editColumn('children_count', function (Category $category) {
                return sprintf('<span class="badge badge-info">%d</span>', $category->children->count());
            })
            ->editColumn('recipes_count', function (Category $category) {
                return sprintf('<span class="badge badge-info">%d</span>', $category->recipes_count);
            })
            ->addColumn('action', function (Category $category) {
                return view('pages/apps.management.categories.columns._actions', compact('category'));
            })
            ->setRowId('id');
    }

    public function query(Category $model): QueryBuilder
    {
        return $model->newQuery()
            ->whereDoesntHave('parent')
            ->withCount('recipes')
            ->orderBy('title');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('categories-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('rt' . "<'row'<'col-sm-12 col-md-5'l><'col-sm-12 col-md-7'p>>")
            ->addTableClass('table align-middle table-row-dashed fs-6 gy-5 dataTable no-footer text-gray-600 fw-semibold')
            ->setTableHeadClass('text-start text-muted fw-bold fs-7 text-uppercase gs-0')
            ->orderBy(1)
            ->drawCallback("function() {" . file_get_contents(resource_path('views/pages/apps/management/categories/columns/_draw-scripts.js')) . "}");
    }

    public function getColumns(): array
    {
        return [
            Column::make('id')->title('ID')->addClass('text-nowrap'),
            Column::make('title')->title('Title')->addClass('text-nowrap'),
            Column::make('children_count')->title('Number of children categories')->searchable(false),
            Column::make('recipes_count')->title('Number of recipes')->searchable(false),
            Column::computed('action')
                ->addClass('text-end text-nowrap')
                ->exportable(false)
                ->printable(false)
                ->width(60)
        ];
    }
}
