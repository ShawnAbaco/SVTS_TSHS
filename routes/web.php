<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdviserController;
use App\Http\Controllers\AdviserCRUDController;
use App\Http\Controllers\PrefectController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PrefectReportController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ParentController;

Route::get('/', function () {
    return view('adviser.login');
});

// Admin routes
Route::prefix('prefect')->group(function () {
    Route::get('/login', [PrefectController::class, 'showLoginForm'])->name('prefect.login');
    Route::post('/login', [PrefectController::class, 'login'])->name('prefect.login.submit');
    Route::get('/studentmanagement', [PrefectController::class, 'studentmanagement'])->name('student.management');
    Route::get('/violationrecords', [PrefectController::class, 'violationrecords'])->name('violation.records');
     Route::get('/parentlists', [PrefectController::class, 'parentlists'])->name('parent.lists');
    Route::get('/violationappointments', [PrefectController::class, 'violationappointments'])->name('violation.appointments');
    Route::get('/peoplecomplaints', [PrefectController::class, 'peoplecomplaints'])->name('people.complaints');
    Route::get('/reportgenerate', [PrefectController::class, 'reportgenerate'])->name('report.generate');
 Route::get('/violationanecdotals', [PrefectController::class, 'violationanecdotals'])->name('violation.anecdotals');
  Route::get('/usermanagement', [PrefectController::class, 'usermanagement'])->name('user.management');
  Route::get('/complaintsappointments', [PrefectController::class, 'complaintsappointments'])->name('complaints.appointments');
  Route::get('/complaintsanecdotals', [PrefectController::class, 'complaintsanecdotals'])->name('complaints.anecdotals');
   Route::get('/offensesandsanctions', [PrefectController::class, 'offensesandsanctions'])->name('offenses.sanctions');
    Route::get('/reports/data/{reportId}', [PrefectReportController::class, 'generateReportData']);
    Route::delete('/students/{student}', [PrefectController::class, 'destroy'])->name('student.delete');


// Store new appointment
    Route::post('/complaints-appointments', [PrefectController::class, 'complaintAppointmentStore'])->name('complaints.appointments.store');

    // Delete appointment
    Route::delete('/complaints-appointments/{id}', [PrefectController::class, 'complaintAppointmentDestroy'])->name('complaints.appointments.destroy');


    // Edit complaint
    Route::get('/complaints/{id}/edit', [PrefectController::class, 'complaintEdit'])->name('complaints.edit');

    // Update complaint
    Route::put('/complaints/{id}', [PrefectController::class, 'complaintUpdate'])->name('complaints.update');

    // Delete complaint
    Route::delete('/complaints/{id}', [PrefectController::class, 'complaintDestroy'])->name('complaints.destroy');


    Route::get('/parents/create', [PrefectController::class, 'parentCreate'])->name('parent.create');
Route::post('/parents', [PrefectController::class, 'parentStore'])->name('parent.store');
Route::get('/parents/{parent}/edit', [PrefectController::class, 'parentEdit'])->name('parent.edit');
Route::put('/parents/{parent}', [PrefectController::class, 'parentUpdate'])->name('parent.update');
Route::delete('/parents/{parent}', [PrefectController::class, 'parentDestroy'])->name('parent.destroy');
    Route::post('violation-appointments/store', [PrefectController::class, 'storeViolationAppointment'])
        ->name('violation.appointments.store');



    // Use 'auth:admin' guard for admin authentication
    Route::middleware('auth:prefect')->group(function () {
        Route::get('/dashboard', [PrefectController::class, 'dashboard'])->name('prefect.dashboard');
        Route::post('/advisers', [PrefectController::class, 'createAdviser'])->name('prefect.create.adviser');
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
    Route::get('/adviser/parentsearch', [StudentController::class, 'parentsearch'])->name('adviser.parentsearch');

    // CRUD student
    Route::post('/students', [StudentController::class, 'store'])->name('students.store');
    Route::put('/students/{id}', [StudentController::class, 'update'])->name('students.update');
    Route::delete('/students/{id}', [StudentController::class, 'destroy'])->name('students.destroy');

    // CRUD parent
    Route::post('/adviser/parents', [ParentController::class, 'parentStore'])->name('parents.store');
    Route::put('/adviser/parents/{id}', [ParentController::class, 'parentUpdate'])->name('parents.update');
    Route::delete('/adviser/parents/{id}', [ParentController::class, 'destroyParent'])->name('parents.destroy');

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



    // Violation Anecdotal
    Route::get('/violation-anecdotal', [AdviserCRUDController::class, 'anecdotalIndex'])->name('violation.anecdotal');
    Route::post('/violation-anecdotal', [AdviserCRUDController::class, 'anecdotalStore'])->name('violation.anecdotal.store');

    // Optional edit/delete routes
    Route::put('/violation-anecdotal/{id}', [AdviserCRUDController::class, 'anecdotalUpdate'])->name('violation.anecdotal.update');
    Route::delete('/violation-anecdotal/{id}', [AdviserCRUDController::class, 'anecdotalDelete'])->name('violation.anecdotal.delete');



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
