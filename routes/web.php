<?php

use App\Http\Controllers\Apps\CategoryManagementController;
use App\Http\Controllers\Apps\SourceManagementController;
use App\Http\Controllers\Apps\UserManagementController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [DashboardController::class, 'index']);

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::name('management.')->group(function () {
        Route::resource('/management/users', UserManagementController::class);
        Route::resource('/management/sources', SourceManagementController::class);
        Route::resource('/management/categories', CategoryManagementController::class);
    });
});

Route::get('/error', function () {
    abort(500);
});

require __DIR__ . '/auth.php';
