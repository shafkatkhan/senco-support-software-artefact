<?php

use App\Http\Controllers\TestFormController;

Route::get('/laravel_welcome', function () {
    return view('welcome');
});

Route::get('/test-form', [TestFormController::class, 'index']);