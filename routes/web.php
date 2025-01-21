<?php

use App\Http\Controllers\Apps\Management\CategoryController;
use App\Http\Controllers\Apps\Management\SourceController;
use App\Http\Controllers\Apps\Management\UserController;
use App\Http\Controllers\Apps\Recipe\IngredientController;
use App\Http\Controllers\Apps\Recipe\RecipeController;
use App\Http\Controllers\Apps\Recipe\StepController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [DashboardController::class, 'index']);

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::name('management.')->prefix('management')->group(function () {
        Route::resource('users', UserController::class);

        Route::resource('sources', SourceController::class);
        Route::post('sources/{source}/collect-urls', [SourceController::class, 'collectUrls']);
        Route::post('sources/{source}/sitemap', [SourceController::class, 'createSitemapUrl']);
        Route::delete('sources/{source}/sitemap/{sourceSitemap}', [SourceController::class, 'deleteSitemapUrl']);
        Route::get('sources/{source}/unparsed-urls', [SourceController::class, 'getUnparsedUrlsView']);

        Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
    });

    Route::name('recipe.')->prefix('recipe')->group(function () {
        Route::get('recipes', [RecipeController::class, 'index'])->name('recipes.index');
        Route::delete('recipes', [RecipeController::class, 'deleteByIds']);
        Route::post('recipes/parse', [RecipeController::class, 'reparseByIds']);
        Route::post('recipes/parse/debug', [RecipeController::class, 'parseDebug']);
        Route::delete('recipes/urls/{sourceRecipeUrl}', [RecipeController::class, 'excludeRecipeUrl']);

        Route::resource('ingredients', IngredientController::class);
        Route::get('ingredients/{ingredient}/details', [IngredientController::class, 'getDetails']);
        Route::get('steps', [StepController::class, 'index'])->name('steps.index');
    });
});

Route::get('/error', function () {
    abort(500);
});

require __DIR__ . '/auth.php';
