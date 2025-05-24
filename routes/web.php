<?php

use Illuminate\Support\Facades\Route;

Route::get('/error', function () {
    abort(500);
});
