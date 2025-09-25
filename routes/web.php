<?php

use App\Http\Controllers\ViolationAppointmentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdviserController;
use App\Http\Controllers\AdviserCRUDController;
use App\Http\Controllers\ComplaintAppointmentController;
use App\Http\Controllers\PrefectController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PrefectReportController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\ViolationAnecdotalController;
use App\Http\Controllers\ViolationRecordController;
use App\Http\Controllers\AuthController;


Route::get('/', function () {
    return view('adviser.login');
});
    Route::get('/adviser/login', [AdviserController::class, 'showLoginForm'])->name('adviser.login');
    Route::post('/adviser/login', [AuthController::class, 'login'])->name('auth.login');


// ===================== Prefect Routes =====================
Route::prefix('prefect')->group(function () {
    // Login / Logout

    Route::post('/logout', [PrefectController::class, 'logout'])->name('prefect.logout');

    // Protected routes
    Route::middleware('auth:prefect')->group(function () {
        Route::get('/dashboard', [PrefectController::class, 'dashboard'])->name('prefect.dashboard');
        Route::post('/advisers', [PrefectController::class, 'createAdviser'])->name('prefect.create.adviser');

        // Management
        Route::get('/studentmanagement', [PrefectController::class, 'studentmanagement'])->name('student.management');
        Route::get('/violationrecords', [PrefectController::class, 'violationrecords'])->name('violation.records');
        Route::get('/parentlists', [PrefectController::class, 'parentlists'])->name('parent.lists');
        Route::get('/usermanagement', [PrefectController::class, 'usermanagement'])->name('user.management');

        // Appointments
        Route::get('/violationappointments', [PrefectController::class, 'violationappointments'])->name('violation.appointments');
        Route::post('violation-appointments/store', [PrefectController::class, 'storeViolationAppointment'])->name('violation.appointments.store');

        Route::get('/complaintsappointments', [PrefectController::class, 'complaintsappointments'])->name('complaints.appointments');
        Route::post('/complaints-appointments', [PrefectController::class, 'complaintAppointmentStore'])->name('complaints.appointments.store');
        Route::delete('/complaints-appointments/{id}', [PrefectController::class, 'complaintAppointmentDestroy'])->name('complaints.appointments.destroy');

        // Complaints
        Route::get('/peoplecomplaints', [PrefectController::class, 'peoplecomplaints'])->name('people.complaints');
        Route::get('/complaints/{id}/edit', [PrefectController::class, 'complaintEdit'])->name('complaints.edit');
        Route::put('/complaints/{id}', [PrefectController::class, 'complaintUpdate'])->name('complaints.update');
        Route::delete('/complaints/{id}', [PrefectController::class, 'complaintDestroy'])->name('complaints.destroy');

        // Anecdotals
        Route::get('/violationanecdotals', [PrefectController::class, 'violationanecdotals'])->name('violation.anecdotals');
        Route::get('/complaintsanecdotals', [PrefectController::class, 'complaintsanecdotals'])->name('complaints.anecdotals');

        // Reports
        Route::get('/reportgenerate', [PrefectController::class, 'reportgenerate'])->name('report.generate');
        Route::get('/reports/data/{reportId}', [PrefectReportController::class, 'generateReportData'])->name('prefect.reports.data');;

        // Parents CRUD
        Route::get('/parents/create', [PrefectController::class, 'parentCreate'])->name('parent.create');
        Route::post('/parents', [PrefectController::class, 'parentStore'])->name('parent.store');
        Route::get('/parents/{parent}/edit', [PrefectController::class, 'parentEdit'])->name('parent.edit');
        Route::put('/parents/{parent}', [PrefectController::class, 'parentUpdate'])->name('parent.update');
        Route::delete('/parents/{parent}', [PrefectController::class, 'parentDestroy'])->name('parent.destroy');

        // Students
        Route::delete('/students/{student}', [PrefectController::class, 'destroy'])->name('student.delete');
        Route::patch('/students/bulk-clear-status', [StudentController::class, 'bulkClearStatus'])
    ->name('prefect.students.bulkClearStatus');
Route::patch('/students/bulk-update-status', [StudentController::class, 'restoreStudents'])
    ->name('prefect.students.bulkRestoreStatus');



        // Offenses & Sanctions
        Route::get('/offensesandsanctions', [PrefectController::class, 'offensesandsanctions'])->name('offenses.sanctions');
    });


});



// ===================== Adviser Routes =====================
Route::prefix('adviser')->group(function () {
    // Login / Logout
    Route::get('/login', [AdviserController::class, 'showLoginForm'])->name('adviser.login');
    Route::post('/logout', [AdviserController::class, 'logout'])->name('adviser.logout');

    // Protected routes
    Route::middleware('auth:adviser')->group(function () {
        Route::get('/dashboard', [AdviserController::class, 'dashboard'])->name('adviser.dashboard');
        Route::get('/violationrecord', [AdviserController::class, 'violationrecord'])->name('violation.record');
        Route::get('/studentlist', [AdviserController::class, 'studentlist'])->name('student.list');
        Route::get('/parentlist', [AdviserController::class, 'parentlist'])->name('parent.list');
        Route::get('/profilesettings', [AdviserController::class, 'profilesettings'])->name('profile.settings');

        // Reports
        Route::get('/adviserreports', [AdviserController::class, 'reports'])->name('adviser.reports');
        Route::get('/reports/data/{reportId}', [ReportController::class, 'getReportData'])->name('adviser.reports.data');

        // Live search
        Route::get('/adviser/parentsearch', [StudentController::class, 'parentsearch'])->name('adviser.parentsearch');
        Route::get('/student/search', [AdviserController::class, 'studentSearch']);
        Route::get('/offense/search', [AdviserController::class, 'offenseSearch']);

        // CRUD student
        Route::post('/students', [StudentController::class, 'store'])->name('students.store');
        Route::put('/students/{id}', [StudentController::class, 'update'])->name('students.update');
        Route::delete('/students/{id}/trash', [StudentController::class, 'trash'])->name('students.trash');
Route::patch('/students/{id}/restore', [StudentController::class, 'restore'])->name('students.restore');
Route::delete('/students/{id}/delete', [StudentController::class, 'forceDelete'])->name('students.forceDelete');
Route::patch('/students/bulk-update-status', [StudentController::class, 'bulkUpdateStatus'])->name('students.bulkUpdateStatus');


        // CRUD parent
        Route::post('/adviser/parents', [ParentController::class, 'parentStore'])->name('parents.store');
        Route::put('/adviser/parents/{id}', [ParentController::class, 'parentUpdate'])->name('parents.update');
        Route::delete('/adviser/parents/{id}', [ParentController::class, 'destroyParent'])->name('parents.destroy');

        // Violation CRUD
        Route::post('/violations', [ViolationRecordController::class, 'storeViolation'])->name('adviser.storeViolation');
        Route::put('/violations/{id}', [ViolationRecordController::class, 'updateViolation'])->name('adviser.violations.update');
        Route::delete('/violations/{id}', [ViolationRecordController::class, 'destroyViolation'])->name('adviser.violations.destroy');

        // Violation Anecdotal
        Route::get('/violationanecdotal', [AdviserController::class, 'violationanecdotal'])->name('violation.anecdotal');
        Route::post('/violation-anecdotal', [ViolationAnecdotalController::class, 'anecdotalStore'])->name('violation.anecdotal.store');
        Route::put('/violation-anecdotal/{id}', [ViolationAnecdotalController::class, 'anecdotalUpdate'])->name('violation.anecdotal.update');
        Route::delete('/violation-anecdotal/{id}', [ViolationAnecdotalController::class, 'anecdotalDelete'])->name('violation.anecdotal.delete');

        // Complaints
        Route::get('/complaintsall', [AdviserController::class, 'complaintsall'])->name('complaints.all');
        Route::get('/complaintsappointment', [AdviserController::class, 'complaintsappointment'])->name('complaints.appointment');
        Route::post('/complaints/appointment/store', [ComplaintAppointmentController::class, 'storeComplaintsAppointment'])->name('complaints.appointment.store');
        Route::get('/complaintsanecdotal', [AdviserController::class, 'complaintsanecdotal'])->name('complaints.anecdotal');

        // Violation Appointment
        Route::get('/violationappointment', [AdviserController::class, 'violationappointment'])->name('violation.appointment');
        Route::post('/adviser/violation/appointment/store', [ViolationAppointmentController::class, 'storeViolationAppointment'])->name('violation.appointment.store');

        // ARCHIVE
        Route::put('/students/{id}/restore', [StudentController::class, 'restore'])->name('students.restore');
        Route::get('/students/archived', [StudentController::class, 'archived'])->name('students.archived');


        //sms
        Route::post('/send-sms-to-parent', [ParentController::class, 'sendSms'])
     ->name('send.sms.to.parent');

        // Offense & Sanction
        Route::get('/offensesanction', [AdviserController::class, 'offensesanction'])->name('offense.sanction');
    });
});
