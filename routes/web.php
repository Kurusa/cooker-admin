<?php

use App\Http\Controllers\Apps\Management\CategoryManagementController;
use App\Http\Controllers\Apps\Management\SourceManagementController;
use App\Http\Controllers\Apps\Management\UserManagementController;
use App\Http\Controllers\Apps\Recipe\IngredientManagementController;
use App\Http\Controllers\Apps\Recipe\RecipeManagementController;
use App\Http\Controllers\Apps\Recipe\StepManagementController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [DashboardController::class, 'index']);

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::name('management.')->prefix('management')->group(function () {
        Route::resource('users', UserManagementController::class);
        Route::resource('sources', SourceManagementController::class);
        Route::get('categories', [CategoryManagementController::class, 'index'])->name('categories.index');
    });

    Route::name('recipe.')->prefix('recipe')->group(function () {
        Route::resource('recipes', RecipeManagementController::class);
        Route::get('recipes/{recipe}/reparse', [RecipeManagementController::class, 'reparseRecipe']);
        Route::post('recipes/reparse', [RecipeManagementController::class, 'reparseByIds']);

        Route::resource('ingredients', IngredientManagementController::class);
        Route::get('ingredients/{ingredient}/details', [IngredientManagementController::class, 'getDetails']);
        Route::get('steps', [StepManagementController::class, 'index'])->name('steps.index');
    });
});

Route::get('/error', function () {
    abort(500);
});

require __DIR__ . '/auth.php';
