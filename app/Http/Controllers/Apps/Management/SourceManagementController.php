<?php

namespace App\Http\Controllers\Apps\Management;

use App\DataTables\Management\SourcesDataTable;
use App\Http\Controllers\Controller;

class SourceManagementController extends Controller
{
    public function index(SourcesDataTable $dataTable)
    {
        return $dataTable->render('pages/apps.management.sources.list');
    }
}
