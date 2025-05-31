<?php

use App\Http\Controllers\DebugController;
use Illuminate\Support\Facades\Route;

Route::get('/error', function () {
    abort(500);
});

Route::get('/debug', [DebugController::class, 'debug']);

Route::post('/import/telegram-recipe', [TelegramRecipeController::class, 'import']);
