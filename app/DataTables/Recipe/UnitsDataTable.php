<?php

namespace App\DataTables\Recipe;

use App\Models\Unit;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class UnitsDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->rawColumns(['ingredients_count'])
            ->addColumn('ingredients_count', function (Unit $unit) {
                return $unit->ingredients()->count();
            })
            ->addColumn('action', function (Unit $unit) {
                return view('pages/apps/recipe/units/columns._actions', compact('unit'));
            })
            ->setRowId('id');
    }

    public function query(Unit $model): QueryBuilder
    {
        return $model->newQuery()->orderBy('title');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('units-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('rt' . "<'row'<'col-sm-12 col-md-5'l><'col-sm-12 col-md-7'p>>")
            ->addTableClass('table align-middle table-row-dashed fs-6 gy-5 dataTable no-footer text-gray-600 fw-semibold')
            ->setTableHeadClass('text-start text-muted fw-bold fs-7 text-uppercase gs-0')
            ->orderBy(1)
            ->pageLength(100)
            ->drawCallback("function() {" . file_get_contents(resource_path('views/pages/apps/recipe/units/columns/_draw-scripts.js')) . "}");
    }

    public function getColumns(): array
    {
        return [
            Column::make('id')->title('ID'),
            Column::make('title')->title('Title')->addClass('text-nowrap'),
            Column::make('ingredients_count')->title('Ingredients count')->addClass('text-nowrap'),
            Column::computed('action')
                ->addClass('text-end text-nowrap')
                ->exportable(false)
                ->printable(false)
                ->width(60)
        ];
    }
}
