<?php

namespace App\Http\Controllers\Apps;

use App\DataTables\CategoriesDataTable;
use App\Http\Controllers\Controller;

class CategoryManagementController extends Controller
{
    public function index(CategoriesDataTable $dataTable)
    {
        return $dataTable->render('pages/apps.management.categories.list');
    }
}
