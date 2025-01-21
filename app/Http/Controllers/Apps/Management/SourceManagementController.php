<?php

namespace App\Http\Controllers\Apps\Management;

use App\DataTables\Management\SourcesDataTable;
use App\Http\Controllers\Controller;
use App\Models\Source;
use App\Models\User;

class SourceManagementController extends Controller
{
    public function index(SourcesDataTable $dataTable)
    {
        return $dataTable->render('pages/apps.management.sources.list');
    }

    public function show(Source $source)
    {
        return view('pages/apps.management.sources.show', compact('source'));
    }
}
