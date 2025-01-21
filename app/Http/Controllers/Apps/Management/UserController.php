<?php

namespace App\Http\Controllers\Apps\Management;

use App\DataTables\Management\UsersDataTable;
use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
    public function index(UsersDataTable $dataTable)
    {
        return $dataTable->render('pages/apps.management.users.list');
    }

    public function show(User $user)
    {
        return view('pages/apps.management.users.show', compact('user'));
    }
}
