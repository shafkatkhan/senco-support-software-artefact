<?php

use App\Http\Controllers\TestFormController;

Route::get('/laravel_welcome', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('login');
});

Route::get('/test-form', [TestFormController::class, 'index']);
Route::post('/test-form', [TestFormController::class, 'store']);
Route::get('/page1', function () {
    return view('test_page');
});
Route::get('/page2', function () {
    return view('test_page');
});
Route::get('/page3', function () {
    return view('test_page');
});