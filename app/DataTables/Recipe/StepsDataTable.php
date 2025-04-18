<?php

namespace App\DataTables\Recipe;

use App\Models\RecipeStep;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class StepsDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->rawColumns(['step', 'recipe_link'])
            ->editColumn('step', function (RecipeStep $step) {
                return view('pages/apps.recipe.steps.columns._step', compact('step'));
            })
            ->addColumn('action', function (RecipeStep $step) {
                return view('pages/apps/recipe/steps/columns._actions', compact('step'));
            })
            ->addColumn('recipe_link', function (RecipeStep $step) {
                return $step->recipe ? '<a href="' . route('recipe.recipes.show', $step->recipe->id) . '">' . $step->recipe->id . '</a>' : 'N/A';
            })
            ->setRowId('id');
    }

    public function query(RecipeStep $model): QueryBuilder
    {
        return $model->newQuery()->with('recipe');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('steps-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('rt' . "<'row'<'col-sm-12 col-md-5'l><'col-sm-12 col-md-7'p>>")
            ->addTableClass('table align-middle table-row-dashed fs-6 gy-5 dataTable no-footer text-gray-600 fw-semibold')
            ->setTableHeadClass('text-start text-muted fw-bold fs-7 text-uppercase gs-0')
            ->orderBy(1)
            ->drawCallback("function() {" . file_get_contents(resource_path('views/pages/apps/recipe/steps/columns/_draw-scripts.js')) . "}");
    }

    public function getColumns(): array
    {
        return [
            Column::make('step')->addClass('d-flex align-items-center')->name('image_url')->searchable(false),
            Column::make('recipe_link')->title('Recipe ID'),
            Column::make('description')->title('Description'),
            Column::computed('action')
                ->addClass('text-end text-nowrap')
                ->exportable(false)
                ->printable(false)
                ->width(60)
        ];
    }
}
