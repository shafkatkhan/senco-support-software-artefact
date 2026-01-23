<?php

use Illuminate\Support\Facades\Route;

Route::get('/laravel_welcome', function () {
    return view('welcome');
});
