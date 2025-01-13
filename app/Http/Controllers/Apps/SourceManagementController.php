<?php

namespace App\Http\Controllers\Apps;

use App\DataTables\SourcesDataTable;
use App\Http\Controllers\Controller;
use App\Models\Source;

class SourceManagementController extends Controller
{
    public function index(SourcesDataTable $dataTable)
    {
        return $dataTable->render('pages/apps.management.sources.list');
    }
}
