<?php

namespace App\Http\Controllers\Apps\Management;

use App\DataTables\Management\SourcesDataTable;
use App\Http\Controllers\Controller;
use App\Models\Source;
use App\Services\Parsers\RecipeParserFactory;
use App\Services\SitemapUrlCollectorService;
use Exception;

class SourceController extends Controller
{
    public function index(SourcesDataTable $dataTable)
    {
        return $dataTable->render('pages/apps.management.sources.list');
    }

    public function show(Source $source)
    {
        return view('pages/apps.management.sources.show', compact('source'));
    }

    public function collectUrls(Source $source)
    {
        try {
            $parser = RecipeParserFactory::make($source->title);
            $service = new SitemapUrlCollectorService($parser, $source);
            $service->getFilteredSitemapUrls();

            return response()->json();
        } catch (Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 500);
        }
    }
}
