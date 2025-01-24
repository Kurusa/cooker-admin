<?php

use App\Http\Controllers\Apps\Management\CategoryController;
use App\Http\Controllers\Apps\Management\SourceController;
use App\Http\Controllers\Apps\Management\UserController;
use App\Http\Controllers\Apps\Recipe\IngredientController;
use App\Http\Controllers\Apps\Recipe\RecipeController;
use App\Http\Controllers\Apps\Recipe\StepController;
use App\Http\Controllers\Apps\Recipe\UnitController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [DashboardController::class, 'index']);

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::name('management.')->prefix('management')->group(function () {
        Route::resource('sources', SourceController::class)->only(['index', 'show']);
        Route::prefix('sources/{source}')->group(function () {
            Route::post('collect-urls', [SourceController::class, 'collectUrls'])->name('sources.collect-urls');
            Route::post('sitemap', [SourceController::class, 'createSitemapUrl'])->name('sources.sitemap');
            Route::post('parse', [SourceController::class, 'parse'])->name('sources.parse');
            Route::delete('sitemap/{sourceSitemap}', [SourceController::class, 'deleteSitemapUrl'])->name('sources.sitemap.delete');
            Route::get('unparsed-urls', [SourceController::class, 'getUnparsedUrlsView'])->name('sources.unparsed-urls');
        });

        Route::resource('categories', CategoryController::class)->only('index', 'show', 'update', 'destroy');
        Route::get('categories/{category}/details', [CategoryController::class, 'getDetails'])->name('categories.details');
    });

    Route::name('recipe.')->prefix('recipe')->group(function () {
        Route::resource('recipes', RecipeController::class)->only(['index']);

        Route::prefix('recipes')->group(function () {
            Route::post('parse', [RecipeController::class, 'reparseByIds'])->name('recipes.parse');
            Route::post('parse/debug', [RecipeController::class, 'parseDebug'])->name('recipes.parse.debug');
            Route::delete('urls/{sourceRecipeUrl}', [RecipeController::class, 'excludeRecipeUrl'])->name('recipes.urls.exclude');
            Route::delete('all', [RecipeController::class, 'deleteAll'])->name('recipes.delete.all');
        });

        Route::delete('recipes', [RecipeController::class, 'deleteByIds']);

        Route::resource('ingredients', IngredientController::class)->only(['index', 'update']);
        Route::get('ingredients/{ingredient}/details', [IngredientController::class, 'getDetails'])->name('ingredients.details');

        Route::resource('units', UnitController::class);
        Route::get('units/{unit}/details', [UnitController::class, 'getDetails']);

        Route::resource('steps', StepController::class)->only(['index']);
    });
});

Route::get('/error', function () {
    abort(500);
});

require __DIR__ . '/auth.php';
