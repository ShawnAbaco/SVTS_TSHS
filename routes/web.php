<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdviserController;
use App\Http\Controllers\AdviserCRUDController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PrefectReportController;

Route::get('/', function () {
    return view('adviser.login');
});

// Admin routes
Route::prefix('prefect')->group(function () {
    Route::get('/login', [AdminController::class, 'showLoginForm'])->name('prefect.login');
    Route::post('/login', [AdminController::class, 'login'])->name('prefect.login.submit');
    Route::get('/studentmanagement', [AdminController::class, 'studentmanagement'])->name('student.management');
    Route::get('/violationrecords', [AdminController::class, 'violationrecords'])->name('violation.records');
     Route::get('/parentlists', [AdminController::class, 'parentlists'])->name('parent.lists');
    Route::get('/violationappointments', [AdminController::class, 'violationappointments'])->name('violation.appointments');
    Route::get('/peoplecomplaints', [AdminController::class, 'peoplecomplaints'])->name('people.complaints');
    Route::get('/reportgenerate', [AdminController::class, 'reportgenerate'])->name('report.generate');
 Route::get('/violationanecdotals', [AdminController::class, 'violationanecdotals'])->name('violation.anecdotals');
  Route::get('/usermanagement', [AdminController::class, 'usermanagement'])->name('user.management');
  Route::get('/complaintsappointments', [AdminController::class, 'complaintsappointments'])->name('complaints.appointments');
  Route::get('/complaintsanecdotals', [AdminController::class, 'complaintsanecdotals'])->name('complaints.anecdotals');
   Route::get('/offensesandsanctions', [AdminController::class, 'offensesandsanctions'])->name('offenses.sanctions');
    Route::get('/reports/data/{reportId}', [PrefectReportController::class, 'generateReportData']);


    // Use 'auth:admin' guard for admin authentication
    Route::middleware('auth:prefect')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('prefect.dashboard');
        Route::post('/advisers', [AdminController::class, 'createAdviser'])->name('prefect.create.adviser');
    });
});

// Adviser routes
Route::prefix('adviser')->group(function () {
    Route::get('/login', [AdviserController::class, 'showLoginForm'])->name('adviser.login');
    Route::post('/login', [AdviserController::class, 'login'])->name('adviser.login.submit');
    Route::get('/violationrecord', [AdviserController::class, 'violationrecord'])->name('violation.record');
    Route::get('/studentlist', [AdviserController::class, 'studentlist'])->name('student.list');
    Route::get('/parentlist', [AdviserController::class, 'parentlist'])->name('parent.list');
    Route::get('/violationappointment', [AdviserController::class, 'violationappointment'])->name('violation.appointment');
    Route::get('/adviserreports', [AdviserController::class, 'reports'])->name('adviser.reports');
    Route::get('/profilesettings', [AdviserController::class, 'profilesettings'])->name('profile.settings');
    
    // live search
    Route::get('/adviser/parentsearch', [AdviserCRUDController::class, 'parentsearch'])->name('adviser.parentsearch');

    // CRUD student
    Route::post('/students', [AdviserCRUDController::class, 'store'])->name('students.store');
    Route::put('/students/{id}', [AdviserCRUDController::class, 'update'])->name('students.update');
    Route::delete('/students/{id}', [AdviserCRUDController::class, 'destroy'])->name('students.destroy');

    // CRUD parent
    Route::post('/adviser/parents', [AdviserCRUDController::class, 'parentStore'])->name('parents.store');
    Route::put('/adviser/parents/{id}', [AdviserCRUDController::class, 'parentUpdate'])->name('parents.update');
    Route::delete('/adviser/parents/{id}', [AdviserCRUDController::class, 'destroyParent'])->name('parents.destroy');

    // CRUD Violation
// Adviser Violation CRUD


    // Live search
Route::get('/student/search', [AdviserController::class, 'studentSearch']);
Route::get('/offense/search', [AdviserController::class, 'offenseSearch']);

    // Violation CRUD
    Route::post('/violations', [AdviserCRUDController::class, 'storeViolation'])->name('adviser.storeViolation');
    Route::put('/violations/{id}', [AdviserCRUDController::class, 'updateViolation'])->name('adviser.violations.update');
    Route::delete('/violations/{id}', [AdviserCRUDController::class, 'destroyViolation'])->name('adviser.violations.destroy');

Route::get('/adviser/reports/data/{reportId}', [ReportController::class, 'getReportData']);




    Route::get('/violationanecdotal', [AdviserController::class, 'violationanecdotal'])->name('violation.anecdotal');
    Route::get('/complaintsall', [AdviserController::class, 'complaintsall'])->name('complaints.all');
    Route::get('/complaintsappointment', [AdviserController::class, 'complaintsappointment'])->name('complaints.appointment');
    Route::get('/complaintsanecdotal', [AdviserController::class, 'complaintsanecdotal'])->name('complaints.anecdotal');
    Route::get('/offensesanction', [AdviserController::class, 'offensesanction'])->name('offense.sanction');

  
    // ✅ fixed logout route name
    Route::post('/logout', [AdviserController::class, 'logout'])->name('adviser.logout');

    // Use 'auth:adviser' guard for adviser authentication
    Route::middleware('auth:adviser')->group(function () {
        Route::get('/dashboard', [AdviserController::class, 'dashboard'])->name('adviser.dashboard');
        Route::get('/reports/data/{reportId}', [ReportController::class, 'getReportData'])->name('adviser.reports.data');
        Route::post('/complaints/appointment/store', [AdviserCRUDController::class, 'storeComplaintsAppointment'])
     ->name('complaints.appointment.store')
     ->middleware('auth:adviser');

        Route::post('/adviser/violation/appointment/store', [AdviserCRUDController::class, 'storeViolationAppointment'])
     ->name('violation.appointment.store');

    });
});
