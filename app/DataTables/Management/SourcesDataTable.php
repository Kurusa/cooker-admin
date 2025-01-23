<?php

namespace App\DataTables\Management;

use App\Models\Source;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class SourcesDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->rawColumns(['id', 'url', 'status', 'recipes_link'])
            ->editColumn('id', function (Source $source) {
                return view('pages/apps.management.sources.columns._source', compact('source'));
            })
            ->editColumn('url', function (Source $source) {
                return sprintf(
                    '<a href="%s" target="_blank">%s</a>',
                    $source->url,
                    $source->title,
                );
            })
            ->editColumn('status', function (Source $source) {
                $statusBadge = '';

                if ($source->isParsingCompleted()) {
                    $statusBadge = '<span class="badge py-3 px-4 fs-7 badge-light-success">Completed</span>';
                }

                if ($source->hasUnparsedRecipes()) {
                    $statusBadge = '<span class="badge py-3 px-4 fs-7 badge-light-primary">Waiting to be parsed</span>';
                }

                if (!$source->sitemaps()->count() && !$source->is_manual) {
                    $statusBadge = '<span class="badge py-3 px-4 fs-7 badge-light-dark">Doesn\'t have a sitemap</span>';
                }

                if ($source->is_manual) {
                    $statusBadge = '<span class="badge py-3 px-4 fs-7 badge-light-secondary">Manual parsing required</span>';
                }

                return $statusBadge;
            })
            ->editColumn('recipes_link', function (Source $source) {
                return view('pages/apps.management.sources.columns._recipes', compact('source'));
            })
            ->addColumn('action', function (Source $source) {
                return view('pages/apps.management.sources.columns._actions', compact('source'));
            })
            ->setRowId('id');
    }

    public function query(Source $model): QueryBuilder
    {
        return $model->newQuery()->withCount('recipes')->orderBy('id');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('sources-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('rt' . "<'row'<'col-sm-12 col-md-5'l><'col-sm-12 col-md-7'p>>")
            ->addTableClass('table align-middle table-row-dashed fs-6 gy-5 dataTable no-footer text-gray-600 fw-semibold')
            ->setTableHeadClass('text-start text-muted fw-bold fs-7 text-uppercase gs-0')
            ->orderBy(1)
            ->drawCallback("function() {" . file_get_contents(resource_path('views/pages/apps/management/sources/columns/_draw-scripts.js')) . "}")
            ->pageLength(20);
    }

    public function getColumns(): array
    {
        return [
            Column::make('id')->title('ID'),
            Column::make('url')->title('Source URL')->addClass('text-nowrap'),
            Column::make('status')->title('Status')->addClass('text-nowrap'),
            Column::make('recipes_link')->title('Recipes')->searchable(false),
            Column::computed('action')
                ->addClass('text-end text-nowrap')
                ->exportable(false)
                ->printable(false)
                ->width(60)
        ];
    }
}
