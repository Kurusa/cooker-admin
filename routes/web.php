<?php

use App\Http\Controllers\CategoryTreeController;
use App\Http\Controllers\DebugController;
use App\Http\Controllers\UnitController;
use Illuminate\Support\Facades\Route;

Route::get('/error', function () {
    abort(500);
});

Route::get('/debug', [DebugController::class, 'debug']);

Route::get('/units', [UnitController::class, 'view']);
Route::post('/units/merge', [UnitController::class, 'merge'])->name('units.merge');

Route::get('/categories', [CategoryTreeController::class, 'index']);
Route::get('/categories/children/{parent?}', [CategoryTreeController::class, 'children'])->where('parent', '[0-9]+');
