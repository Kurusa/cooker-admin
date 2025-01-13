<?php

namespace App\Http\Controllers\Apps\Management;

use App\DataTables\Management\CategoriesDataTable;
use App\Http\Controllers\Controller;

class CategoryManagementController extends Controller
{
    public function index(CategoriesDataTable $dataTable)
    {
        return $dataTable->render('pages/apps.management.categories.list');
    }
}
