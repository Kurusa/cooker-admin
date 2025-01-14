<?php

namespace App\DataTables\Recipe;

use App\Models\Ingredient;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class IngredientsDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->rawColumns(['recipes_count'])
            ->addColumn('action', function (Ingredient $ingredient) {
                return view('pages/apps/recipe/ingredients/columns._actions', compact('ingredient'));
            })
            ->editColumn('recipes_count', function (Ingredient $ingredient) {
                return sprintf('<span class="badge badge-info">%d</span>', $ingredient->recipes_count);
            })
            ->setRowId('id');
    }

    public function query(Ingredient $model): QueryBuilder
    {
        return $model->newQuery()->withCount('recipes');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('ingredients-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('rt' . "<'row'<'col-sm-12 col-md-5'l><'col-sm-12 col-md-7'p>>")
            ->addTableClass('table align-middle table-row-dashed fs-6 gy-5 dataTable no-footer text-gray-600 fw-semibold')
            ->setTableHeadClass('text-start text-muted fw-bold fs-7 text-uppercase gs-0')
            ->orderBy(1)
            ->drawCallback("function() {" . file_get_contents(resource_path('views/pages/apps/recipe/ingredients/columns/_draw-scripts.js')) . "}");
    }

    public function getColumns(): array
    {
        return [
            Column::make('id')->title('ID'),
            Column::make('title')->title('Title')->addClass('text-nowrap'),
            Column::make('recipes_count')->title('Number of recipes')->searchable(false),
            Column::computed('action')
                ->addClass('text-end text-nowrap')
                ->exportable(false)
                ->printable(false)
                ->width(60)
        ];
    }
}
