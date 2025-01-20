<?php

namespace App\DataTables\Management;

use App\Exceptions\UnknownSourceException;
use App\Models\Source;
use App\Services\Parsers\RecipeParserFactory;
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
            ->rawColumns(['id', 'url', 'recipes_count'])
            ->editColumn('url', function (Source $source) {
                return sprintf('<a href="%s" target="_blank">%s</a>', $source->url, $source->url);
            })
            ->editColumn('recipes_count', function (Source $source) {
                try {
                    $parser = RecipeParserFactory::make($source->title);
                    $unparsedUrlsCount = count($parser->getFilteredSitemapUrls($source));
                } catch (UnknownSourceException) {
                    $unparsedUrlsCount = '?';
                }

                return sprintf(
                    '<span class="badge badge-info">%d %s</span>',
                    $source->recipes_count,
                    '(out of ' . $unparsedUrlsCount . ')',
                );
            })
            ->addColumn('action', function (Source $source) {
                return view('pages/apps.management.sources.columns._actions', compact('source'));
            })
            ->setRowId('id');
    }

    public function query(Source $model): QueryBuilder
    {
        return $model->newQuery()->withCount('recipes');
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
            Column::make('recipes_count')->title('Number of recipes')->searchable(false),
            Column::computed('action')
                ->addClass('text-end text-nowrap')
                ->exportable(false)
                ->printable(false)
                ->width(60)
        ];
    }
}
