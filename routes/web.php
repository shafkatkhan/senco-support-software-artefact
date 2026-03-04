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
use \App\Http\Controllers\PupilAccommodationController;
use \App\Http\Controllers\RecordTypeController;
use \App\Http\Controllers\RecordController;
use \App\Http\Controllers\ProfessionalController;
use \App\Http\Controllers\MeetingController;
use \App\Http\Controllers\MeetingTypeController;
use \App\Http\Controllers\EventController;

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

    Route::get('/pupil-details/{pupil}/summary', [PupilController::class, 'show'])->name('pupils.show');
    Route::get('/pupil-details/{pupil}/medications', [PupilController::class, 'medications'])->name('pupils.medications');
    Route::get('/pupil-details/{pupil}/diagnoses', [PupilController::class, 'diagnoses'])->name('pupils.diagnoses');
    Route::get('/pupil-details/{pupil}/records', [PupilController::class, 'records'])->name('pupils.records');
    Route::get('/pupil-details/{pupil}/events', [PupilController::class, 'events'])->name('pupils.events');
    Route::get('/pupil-details/{pupil}/accommodations', [PupilController::class, 'accommodations'])->name('pupils.accommodations');
    Route::get('/pupil-details/{pupil}/family-members', [PupilController::class, 'familyMembers'])->name('pupils.family_members');
    Route::get('/pupil-details/{pupil}/meetings', [PupilController::class, 'meetings'])->name('pupils.meetings');

    Route::resource('pupils', PupilController::class)->except(['create', 'edit', 'show']);

    Route::resource('accommodations', AccommodationController::class)->except(['create', 'show', 'edit']);

    Route::resource('medications', MedicationController::class)->only(['store', 'update', 'destroy']);

    Route::resource('diagnoses', DiagnosisController::class)->only(['store', 'update', 'destroy']);

    Route::resource('records', RecordController::class)->only(['store', 'update', 'destroy']);

    Route::resource('events', EventController::class)->only(['store', 'update', 'destroy']);

    Route::resource('family-members', FamilyMemberController::class)->only(['store', 'update', 'destroy']);

    Route::resource('meetings', MeetingController::class)->only(['store', 'update', 'destroy']);

    Route::post('/pupils/{pupil}/accommodations', [PupilAccommodationController::class, 'store'])->name('pupils.accommodations.store');
    Route::delete('/pupils/{pupil}/accommodations/{accommodation}', [PupilAccommodationController::class, 'destroy'])->name('pupils.accommodations.destroy');

    Route::resource('record-types', RecordTypeController::class)->except(['create', 'show', 'edit']);
    
    Route::resource('professionals', ProfessionalController::class)->except(['create', 'show', 'edit']);

    Route::resource('meeting-types', MeetingTypeController::class)->except(['create', 'show', 'edit']);
});