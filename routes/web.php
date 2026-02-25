<?php

use App\Http\Controllers\InstallController;
use App\Http\Controllers\TestFormController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserGroupController;
use \App\Http\Controllers\UserController;
use \App\Http\Controllers\PupilController;
use \App\Http\Controllers\AccommodationController;
use \App\Http\Controllers\MedicationController;
use \App\Http\Controllers\DiagnosisController;
use \App\Http\Controllers\FamilyMemberController;

Route::get('/debug-session', function () {
    return response()->json(session()->all());
});

Route::get('/laravel_welcome', function () {
    return view('welcome');
});

Route::get('/install', [InstallController::class, 'index'])->name('install.index');
Route::post('/install/process', [InstallController::class, 'process'])->name('install.process');
Route::get('/install/lang_setup', [InstallController::class, 'lang_setup_view'])->name('install.lang_setup_view');
Route::post('/install/lang_setup', [InstallController::class, 'lang_setup'])->name('install.lang_setup');

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

    Route::resource('users', UserController::class)->except(['create', 'edit']);

    Route::get('/pupil-details/{id}/summary', [PupilController::class, 'show'])->name('pupils.show');
    Route::get('/pupil-details/{id}/medications', [PupilController::class, 'medications'])->name('pupils.medications');
    Route::get('/pupil-details/{id}/diagnoses', [PupilController::class, 'diagnoses'])->name('pupils.diagnoses');
    Route::get('/pupil-details/{id}/family-members', [PupilController::class, 'familyMembers'])->name('pupils.family_members');
    Route::resource('pupils', PupilController::class)->except(['create', 'edit', 'show']);

    Route::resource('accommodations', AccommodationController::class)->except(['create', 'show', 'edit']);

    Route::resource('medications', MedicationController::class)->only(['store', 'update', 'destroy']);

    Route::resource('diagnoses', DiagnosisController::class)->only(['store', 'update', 'destroy']);

    Route::resource('family-members', FamilyMemberController::class)->only(['store', 'update', 'destroy']);
});