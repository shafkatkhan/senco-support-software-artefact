<?php

use App\Http\Controllers\TestFormController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserGroupController;
use \App\Http\Controllers\UserController;

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
    
    Route::get('/test-form', [TestFormController::class, 'index'])->name('test-form.index');
    Route::post('/test-form', [TestFormController::class, 'store'])->name('test-form.store');
    
    Route::get('/page1', function () {
        return view('test_page', ['title' => 'Page 1']);
    })->name('page1');

    Route::get('/page2', function () {
        return view('test_page', ['title' => 'Page 2']);
    })->name('page2');

    Route::get('/page3', function () {
        return view('test_page', ['title' => 'Page 3']);
    })->name('page3');

    Route::resource('user-groups', UserGroupController::class)->except(['create', 'show', 'edit']);

    Route::resource('users', UserController::class)->except(['create', 'show', 'edit', 'store', 'update']);
});