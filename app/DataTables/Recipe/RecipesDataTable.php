<?php

namespace App\DataTables\Recipe;

use App\Models\Recipe;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class RecipesDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', function (Recipe $recipe) {
                return view('pages/apps/recipe/recipes/columns._actions', compact('recipe'));
            })
            ->setRowId('id');
    }

    public function query(Recipe $model): QueryBuilder
    {
        return $model->newQuery()->with('category');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('recipes-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('rt' . "<'row'<'col-sm-12 col-md-5'l><'col-sm-12 col-md-7'p>>")
            ->addTableClass('table align-middle table-row-dashed fs-6 gy-5 dataTable no-footer text-gray-600 fw-semibold')
            ->setTableHeadClass('text-start text-muted fw-bold fs-7 text-uppercase gs-0')
            ->orderBy(1)
            ->drawCallback("function() {" . file_get_contents(resource_path('views/pages/apps/recipe/recipe/columns/_draw-scripts.js')) . "}");
    }

    public function getColumns(): array
    {
        return [
            Column::make('title')->title('Title')->addClass('text-nowrap'),
            Column::make('complexity')->title('Complexity')->searchable(false),
            Column::make('portions')->title('Portions')->searchable(false),
            Column::computed('action')
                ->addClass('text-end text-nowrap')
                ->exportable(false)
                ->printable(false)
                ->width(60)
        ];
    }
}
