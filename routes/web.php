<?php

use App\Http\Controllers\DebugController;
use App\Http\Controllers\UnitController;
use Illuminate\Support\Facades\Route;

Route::get('/error', function () {
    abort(500);
});

Route::get('/debug', [DebugController::class, 'debug']);
Route::post('/units/merge', [UnitController::class, 'merge'])->name('units.merge');
