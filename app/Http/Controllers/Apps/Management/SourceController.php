<?php

namespace App\Http\Controllers\Apps\Management;

use App\DataTables\Management\SourcesDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Source\CreateSitemapUrlRequest;
use App\Models\Source;
use App\Models\SourceSitemap;
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

    public function createSitemapUrl(Source $source, CreateSitemapUrlRequest $request)
    {
        $source->sitemaps()->create($request->validated());

        return response()->json([
            'success' => true,
        ]);
    }

    public function deleteSitemapUrl(Source $source, SourceSitemap $sourceSitemap)
    {
        if ($source->sitemaps()->where('id', $sourceSitemap->id)->exists()) {
            $sourceSitemap->delete();

            return response()->json([
                'success' => true,
            ]);
        }

        return response()->json([
            'error' => 'This source sitemap does not belong to this source.',
        ]);
    }
}
