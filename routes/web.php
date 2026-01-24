<?php

use App\Http\Controllers\TestFormController;
use App\Http\Controllers\LoginController;

Route::get('/laravel_welcome', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('login');
})->middleware('guest')->name('login');

Route::post('/login', [LoginController::class, 'login']);

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // temporary redirect
    Route::get('/', function () {
        return redirect('/page1');
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

});