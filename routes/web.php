<?php

use Illuminate\Support\Facades\Route;

Route::get('/laravel_welcome', function () {
    return view('welcome');
});

Route::get('/test-form', function () {
    return view('test_form');
});