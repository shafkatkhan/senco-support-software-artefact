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
use \App\Http\Controllers\RecordTypeController;
use \App\Http\Controllers\RecordController;
use \App\Http\Controllers\ProfessionalController;
use \App\Http\Controllers\MeetingController;
use \App\Http\Controllers\MeetingTypeController;
use \App\Http\Controllers\EventController;
use \App\Http\Controllers\BackupController;
use \App\Http\Controllers\SubjectController;
use \App\Http\Controllers\MajorController;
use \App\Http\Controllers\ProficiencyController;
use \App\Http\Controllers\DietController;
use \App\Http\Controllers\MfaSettingController;
use \App\Http\Controllers\MfaSetupController;
use \App\Http\Controllers\MfaChallengeController;
use \App\Http\Controllers\EmailSettingController;
use \App\Http\Controllers\PermissionController;
use \App\Http\Controllers\SchoolHistoryController;
use \App\Http\Controllers\AttachmentController;
use \App\Http\Controllers\PupilProgressionController;
use \App\Http\Controllers\ProgressionSettingController;
use \App\Http\Controllers\ReportController;

Route::get('/debug-session', function () {
    return response()->json(session()->all());
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

    Route::get('/', function () {
        return redirect('/pupils');
    });
    
    Route::get('/test-form', [TestFormController::class, 'index'])->name('test-form.index');
    Route::post('/test-form', [TestFormController::class, 'store'])->name('test-form.store');

    Route::resource('user-groups', UserGroupController::class)->except(['create', 'show', 'edit']);

    Route::resource('users', UserController::class)->except(['create', 'edit']);

    Route::get('/pupil-details/{pupil}/summary', [PupilController::class, 'show'])->name('pupils.show');
    Route::get('/pupil-details/{pupil}/edit', [PupilController::class, 'edit'])->name('pupils.edit');
    Route::get('/pupil-details/{pupil}/medications', [PupilController::class, 'medications'])->name('pupils.medications');
    Route::get('/pupil-details/{pupil}/diagnoses', [PupilController::class, 'diagnoses'])->name('pupils.diagnoses');
    Route::get('/pupil-details/{pupil}/records', [PupilController::class, 'records'])->name('pupils.records');
    Route::get('/pupil-details/{pupil}/events', [PupilController::class, 'events'])->name('pupils.events');
    Route::get('/pupil-details/{pupil}/family-members', [PupilController::class, 'familyMembers'])->name('pupils.family_members');
    Route::get('/pupil-details/{pupil}/meetings', [PupilController::class, 'meetings'])->name('pupils.meetings');
    Route::get('/pupil-details/{pupil}/diet', [PupilController::class, 'diets'])->name('pupils.diets');
    Route::get('/pupil-details/{pupil}/school-history', [PupilController::class, 'schoolHistories'])->name('pupils.school_histories');
    Route::get('/pupil-details/{pupil}/attachments', [PupilController::class, 'attachments'])->name('pupils.attachments')->middleware('can:manage-attachments');
    Route::get('/pupil-details/{pupil}/progressions', [PupilController::class, 'progressions'])->name('pupils.progressions');

    Route::resource('pupils', PupilController::class)->except(['edit', 'show']);
    Route::get('/pupils/{pupil}/export', [PupilController::class, 'export'])->name('pupils.export')->middleware('can:export-pupil-data');
    Route::post('/pupils/extract-file', [PupilController::class, 'extractFromFile'])->name('pupils.extract-file');
    Route::post('/pupils/{pupil}/treatment-plan-updates', [PupilController::class, 'updateTreatmentPlan'])->name('pupils.treatment_plan_updates.store');
    
    Route::resource('pupil-progressions', PupilProgressionController::class)->only(['store', 'update', 'destroy'])->middleware('can:manage-pupil-progressions');
    Route::put('/pupils/{pupil}/toggle-auto-progression', [PupilProgressionController::class, 'toggleAutoProgression'])->name('pupils.toggle-auto-progression')->middleware('can:manage-pupil-progressions');
    Route::get('/pupils/{pupil}/pupil-progressions/export/{format}', [PupilProgressionController::class, 'exportSpreadsheet'])->name('pupils.pupil-progressions.export')->middleware('can:export-pupil-data');

    Route::resource('accommodations', AccommodationController::class)->except(['create', 'show', 'edit']);

    Route::resource('medications', MedicationController::class)->only(['store', 'update', 'destroy']);
    Route::post('/medications/extract-file', [MedicationController::class, 'extractFromFile'])->name('medications.extract-file');
    Route::get('/pupils/{pupil}/medications/export/{format}', [MedicationController::class, 'exportSpreadsheet'])->name('pupils.medications.export')->middleware('can:export-pupil-data');

    Route::resource('diagnoses', DiagnosisController::class)->only(['store', 'update', 'destroy']);
    Route::post('/diagnoses/extract-file', [DiagnosisController::class, 'extractFromFile'])->name('diagnoses.extract-file');
    Route::get('/pupils/{pupil}/diagnoses/export/{format}', [DiagnosisController::class, 'exportSpreadsheet'])->name('pupils.diagnoses.export')->middleware('can:export-pupil-data');

    Route::resource('records', RecordController::class)->only(['store', 'update', 'destroy']);
    Route::post('/records/extract-file', [RecordController::class, 'extractFromFile'])->name('records.extract-file');
    Route::get('/pupils/{pupil}/records/export/{format}', [RecordController::class, 'exportSpreadsheet'])->name('pupils.records.export')->middleware('can:export-pupil-data');

    Route::resource('events', EventController::class)->only(['store', 'update', 'destroy']);
    Route::post('/events/extract-file', [EventController::class, 'extractFromFile'])->name('events.extract-file');
    Route::get('/pupils/{pupil}/events/export/{format}', [EventController::class, 'exportSpreadsheet'])->name('pupils.events.export')->middleware('can:export-pupil-data');

    Route::resource('family-members', FamilyMemberController::class)->only(['store', 'update', 'destroy']);
    Route::post('/family-members/extract-file', [FamilyMemberController::class, 'extractFromFile'])->name('family-members.extract-file');
    Route::get('/pupils/{pupil}/family-members/export/{format}', [FamilyMemberController::class, 'exportSpreadsheet'])->name('pupils.family-members.export')->middleware('can:export-pupil-data');

    Route::resource('meetings', MeetingController::class)->only(['store', 'update', 'destroy']);
    Route::post('/meetings/extract-file', [MeetingController::class, 'extractFromFile'])->name('meetings.extract-file');
    Route::get('/pupils/{pupil}/meetings/export/{format}', [MeetingController::class, 'exportSpreadsheet'])->name('pupils.meetings.export')->middleware('can:export-pupil-data');
    
    Route::resource('diets', DietController::class)->only(['store', 'update', 'destroy']);
    Route::get('/pupils/{pupil}/diets/export/{format}', [DietController::class, 'exportSpreadsheet'])->name('pupils.diets.export')->middleware('can:export-pupil-data');

    Route::resource('school-histories', SchoolHistoryController::class)->only(['store', 'update', 'destroy']);
    Route::post('/school-histories/extract-file', [SchoolHistoryController::class, 'extractFromFile'])->name('school-histories.extract-file');
    Route::get('/pupils/{pupil}/school-histories/export/{format}', [SchoolHistoryController::class, 'exportSpreadsheet'])->name('pupils.school-histories.export')->middleware('can:export-pupil-data');

    Route::put('/attachments/{attachment}/transcript', [AttachmentController::class, 'updateTranscript'])->name('attachments.update_transcript')->middleware('can:manage-attachments');

    Route::resource('record-types', RecordTypeController::class)->except(['create', 'show', 'edit']);
    
    Route::resource('professionals', ProfessionalController::class)->except(['create', 'show', 'edit']);

    Route::resource('meeting-types', MeetingTypeController::class)->except(['create', 'show', 'edit']);

    Route::get('/backups', [BackupController::class, 'index'])->name('backups.index');
    Route::post('/backups', [BackupController::class, 'store'])->name('backups.store');
    Route::get('/backups/download/{file_path}', [BackupController::class, 'download'])->name('backups.download')->where('file_path', '.*');
    Route::delete('/backups/delete/{file_path}', [BackupController::class, 'destroy'])->name('backups.destroy')->where('file_path', '.*');

    Route::resource('subjects', SubjectController::class)->except(['create', 'show', 'edit']);

    Route::resource('majors', MajorController::class)->except(['create', 'show', 'edit']);

    Route::resource('proficiencies', ProficiencyController::class)->except(['create', 'show', 'edit']);

    Route::get('/mfa-settings', [MfaSettingController::class, 'index'])->name('mfa-settings.index');
    Route::put('/mfa-settings', [MfaSettingController::class, 'update'])->name('mfa-settings.update');

    Route::get('/email-settings', [EmailSettingController::class, 'index'])->name('email-settings.index');
    Route::put('/email-settings', [EmailSettingController::class, 'update'])->name('email-settings.update');
    Route::post('/email-settings/test', [EmailSettingController::class, 'test'])->name('email-settings.test');

    Route::get('/mfa-setup', [MfaSetupController::class, 'index'])->name('mfa-setup.index');
    Route::post('/mfa-setup', [MfaSetupController::class, 'verify'])->name('mfa-setup.verify');
    Route::post('/mfa-setup/reset', [MfaSetupController::class, 'reset'])->name('mfa-setup.reset');

    Route::get('/mfa-challenge', [MfaChallengeController::class, 'index'])->name('mfa-challenge.index');
    Route::post('/mfa-challenge', [MfaChallengeController::class, 'verify'])->name('mfa-challenge.verify');

    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::post('/permissions', [PermissionController::class, 'update'])->name('permissions.update');

    Route::get('/attachments/{attachment}', [AttachmentController::class, 'show'])->name('attachments.show');
    Route::delete('/attachments/{attachment}', [AttachmentController::class, 'destroy'])->name('attachments.destroy');

    Route::get('/progression-settings', [ProgressionSettingController::class, 'index'])->name('progression-settings.index')->middleware('can:manage-school-progression-settings');
    Route::put('/progression-settings', [ProgressionSettingController::class, 'update'])->name('progression-settings.update')->middleware('can:manage-school-progression-settings');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index')->middleware('can:export-cohort-reports');
    Route::get('/reports/preview', [ReportController::class, 'preview'])->name('reports.preview')->middleware('can:export-cohort-reports');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export')->middleware('can:export-cohort-reports');
});