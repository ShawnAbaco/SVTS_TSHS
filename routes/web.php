<?php

use App\Http\Controllers\ViolationAppointmentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Prefect\PStudentController;
use App\Http\Controllers\AdviserController;
use App\Http\Controllers\AdviserCRUDController;
use App\Http\Controllers\ComplaintAppointmentController;
use App\Http\Controllers\PrefectController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PrefectReportController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\ViolationAnecdotalController;
use App\Http\Controllers\ViolationRecordController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Prefect\PAdviserController;
use App\Http\Controllers\Prefect\PComplaintController;
use App\Http\Controllers\Prefect\PParentController;

Route::get('/', function () {
    return view('adviser.login');
});
    Route::get('/adviser/login', [AdviserController::class, 'showLoginForm'])->name('adviser.login');
    Route::post('/adviser/login', [AuthController::class, 'login'])->name('auth.login');
    Route::get('/login', [AdviserController::class, 'showLoginForm'])->name('login');


// ===================== Prefect Routes =====================
Route::prefix('prefect')->group(function () {
    // Login / Logout
    Route::post('/logout', [PrefectController::class, 'logout'])->name('prefect.logout');

    // Protected routes
    Route::middleware('auth:prefect')->group(function () {
        Route::get('/dashboard', [PrefectController::class, 'dashboard'])->name('prefect.dashboard');
        // Route::post('/advisers', [PrefectController::class, 'createAdviser'])->name('prefect.create.adviser');
        Route::post('/adviser/store', [PAdviserController::class, 'store'])->name('adviser.store');




// new student store
Route::post('/students/store', [PStudentController::class, 'store'])->name('students.store');



    // Create Complaint Form
    Route::get('/complaints/create', [PComplaintController::class, 'create'])->name('complaints.create');

    // Store Complaints
Route::post('/complaints/store', [PComplaintController::class, 'store'])->name('complaints.store');

    // AJAX: Search students for complainant/respondent
    Route::post('/complaints/search-students', [PComplaintController::class, 'searchStudents'])
        ->name('complaints.search-students');

    // AJAX: Search offenses
    Route::post('/complaints/search-offenses', [PComplaintController::class, 'searchOffenses'])
        ->name('complaints.search-offenses');

    // AJAX: Get sanction for offense (optional)
    Route::get('/complaints/get-sanction', [PComplaintController::class, 'getSanction'])
        ->name('complaints.get-sanction');






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

// diri ang bag o
        Route::get('/create/parent', [ParentController::class, 'createParent'])->name('create.parent');
        Route::post('/parents/store', [PParentController::class, 'store'])->name('parents.store');

        Route::get('/create/student', [StudentController::class, 'createStudent'])->name('create.student');
        Route::get(uri: '/create/adviser', action: [PrefectController::class, 'createAdviser'])->name('create.adviser');



        Route::post('/students/bulk-delete', [StudentController::class, 'bulkDestroy'])
     ->name('students.bulk-destroy');

Route::get('/students/by-parent/{parentId}', [StudentController::class, 'getByParent'])
     ->name('students.by-parent');


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


    // NEW VIOLATION RECORD CONTROLLER
    Route::get('/violations/create', [ViolationRecordController::class, 'create'])->name('violations.create');
Route::post('/violations', [ViolationRecordController::class, 'store'])->name('violations.store');

Route::post('/violations/search-students', [ViolationRecordController::class, 'searchStudents'])->name('violations.search-students');
Route::post('/violations/search-offenses', [ViolationRecordController::class, 'searchOffenses'])->name('violations.search-offenses');

Route::get('/violations/get-sanction', [ViolationRecordController::class, 'getSanction'])->name('violations.get-sanction');

// NEW COMPLAINT RECORD CONTROLLER
Route::get('/complaints/create', [ComplaintController::class, 'create'])->name('complaints.create');
Route::post('/complaints/store', [ComplaintController::class, 'store'])->name('complaints.store');
Route::post('/complaints/search-students', [ComplaintController::class, 'searchStudents'])->name('complaints.search-students');
Route::post('/complaints/search-types', [ComplaintController::class, 'searchTypes'])->name('complaints.search-types');
Route::get('/complaints/get-action', [ComplaintController::class, 'getAction'])->name('complaints.get-action');


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
        // Route::post('/students', [StudentController::class, 'store'])->name('students.store');
        Route::put('/students/{id}', [StudentController::class, 'update'])->name('students.update');
        Route::delete('/students/{id}/trash', [StudentController::class, 'trash'])->name('students.trash');
Route::patch('/students/{id}/restore', [StudentController::class, 'restore'])->name('students.restore');
Route::delete('/students/{id}/delete', [StudentController::class, 'forceDelete'])->name('students.forceDelete');
Route::patch('/students/bulk-update-status', [StudentController::class, 'bulkUpdateStatus'])->name('students.bulkUpdateStatus');


        // CRUD parent
        // Route::post('/adviser/parents', [ParentController::class, 'parentStore'])->name('parents.store');
        Route::put('/adviser/parents/{id}', [ParentController::class, 'parentUpdate'])->name('parents.update');
        Route::delete('/adviser/parents/{id}', [ParentController::class, 'destroyParent'])->name('parents.destroy');





            // NEW VIOLATION RECORD CONTROLLER ADVISER
    Route::get('/violations/create', [ViolationRecordController::class, 'Acreate'])->name('Aviolations.create');
Route::post('/violations', [ViolationRecordController::class, 'Astore'])->name('Aviolations.store');

Route::post('/violations/search-students', [ViolationRecordController::class, 'AsearchStudents'])->name('Aviolations.search-students');
Route::post('/violations/search-offenses', [ViolationRecordController::class, 'AsearchOffenses'])->name('Aviolations.search-offenses');

Route::get('/violations/get-sanction', [ViolationRecordController::class, 'AgetSanction'])->name('Aviolations.get-sanction');





        // // Violation CRUD
        // Route::post('/violations', [ViolationRecordController::class, 'storeViolation'])->name('adviser.storeViolation');
        // Route::put('/violations/{id}', [ViolationRecordController::class, 'updateViolation'])->name('adviser.violations.update');
        // Route::delete('/violations/{id}', [ViolationRecordController::class, 'destroyViolation'])->name('adviser.violations.destroy');

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
