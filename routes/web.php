<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LogoutController;

// Prefect Controllers
use App\Http\Controllers\Prefect\PDashboardController;
use App\Http\Controllers\Prefect\PStudentController;
use App\Http\Controllers\Prefect\PAdviserController;
use App\Http\Controllers\Prefect\PParentController;
use App\Http\Controllers\Prefect\PViolationController;
use App\Http\Controllers\Prefect\PViolationAppointmentController;
use App\Http\Controllers\Prefect\PComplaintController;
use App\Http\Controllers\Prefect\POffenseSanctionController;
use App\Http\Controllers\Prefect\PReportController;
use App\Http\Controllers\Prefect\PViolationAnecdotalController;
use App\Http\Controllers\Prefect\ProfileController;
use App\Http\Controllers\Prefect\PComplaintAnecdotalController;
use App\Http\Controllers\PLogoutController;

// Adviser New Controllers
use App\Http\Controllers\Adviser\NewAdviser\ADashboardController;
use App\Http\Controllers\Adviser\NewAdviser\AStudentController;
use App\Http\Controllers\Adviser\NewAdviser\AAdviserController;
use App\Http\Controllers\Adviser\NewAdviser\AParentController;
use App\Http\Controllers\Adviser\NewAdviser\AViolationController;
use App\Http\Controllers\Adviser\NewAdviser\AViolationAppointmentController;
use App\Http\Controllers\Adviser\NewAdviser\AComplaintController;
use App\Http\Controllers\Adviser\NewAdviser\AOffenseSanctionController;
use App\Http\Controllers\Adviser\NewAdviser\AReportController;
use App\Http\Controllers\Adviser\NewAdviser\AViolationAnecdotalController;
use App\Http\Controllers\Adviser\NewAdviser\AProfileController;
use App\Http\Controllers\Adviser\NewAdviser\AComplaintAnecdotalController;

// Adviser Controllers
// use App\Http\Controllers\Adviser\AProfileController;
// use App\Http\Controllers\Adviser\ADashboardController;
// use App\Http\Controllers\Adviser\ALogoutController;
// use App\Http\Controllers\Adviser\AStudentController;
// use App\Http\Controllers\Adviser\AParentController;
// use App\Http\Controllers\Adviser\AViolationController;
// use App\Http\Controllers\Adviser\AOffenseSanctionController;
// use App\Http\Controllers\Adviser\AReportController;
use Barryvdh\DomPDF\Facade\Pdf;



Route::get('/', function () {
    return view('login');
});



// ===================== Authentication Routes =====================
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/', [AuthController::class, 'login'])->name('login');
// Password reset routes
Route::post('/password/forgot', [AuthController::class, 'sendPasswordResetCode'])->name('password.forgot');
Route::post('/password/verify-code', [AuthController::class, 'verifyResetCode'])->name('password.verify-code');
Route::post('/password/reset', [AuthController::class, 'resetPassword'])->name('password.reset');

Route::get('/logout', [PLogoutController::class, 'logout'])->name('prefect.logout');


// ===================== Prefect Routes =====================
Route::prefix('prefect')->group(function () {
    // Logout
    // Protected routes
    Route::middleware('auth:prefect')->group(function () {
        // Dashboard
        Route::get('/dashboard', [PDashboardController::class, 'dashboard'])->name('prefect.dashboard');

        // Notifications Route - ADD THIS LINE
        Route::get('/notifications', [PDashboardController::class, 'notifications'])->name('prefect.notifications');

        // Profile routes
        Route::post('/send-verification-code', [ProfileController::class, 'sendVerificationCode'])->name('prefect.send-verification-code');
        Route::post('/change-password', [ProfileController::class, 'changePassword'])->name('prefect.change-password');
        Route::get('/profile/info', [ProfileController::class, 'getProfileInfo'])->name('prefect.profile-info');
        Route::post('/profile/upload-image', [ProfileController::class, 'uploadProfileImage'])->name('prefect.upload-profile-image');
        Route::post('/profile/remove-image', [ProfileController::class, 'removeProfileImage'])->name('prefect.remove-profile-image');

            // notification route
        // Notification routes
    Route::post('/notifications/mark-all-read', [PViolationController::class, 'markAllNotificationsAsRead'])
        ->name('prefect.notifications.mark-all-read');

    Route::post('/notifications/delete-read', [PViolationController::class, 'deleteReadNotifications'])
        ->name('prefect.notifications.delete-read');



        // Management Routes
        Route::get('/studentmanagement', [PStudentController::class, 'studentmanagement'])->name('student.management');
        Route::get('/parentlists', [PParentController::class, 'parentlists'])->name('parent.lists');
        Route::get('/offensesandsanctions', [POffenseSanctionController::class, 'index'])->name('offenses.sanctions');


        // Student Routes
        Route::get('/create/student', [PStudentController::class, 'createStudent'])->name('create.student');
        Route::post('/students/store', [PStudentController::class, 'store'])->name('students.store');
        Route::put('/students/update', [PStudentController::class, 'update'])->name('prefect.students.update');
        Route::get('/students/search', [PStudentController::class, 'search'])->name('students.search');
        Route::get('/students/all', [PStudentController::class, 'getAllStudents'])->name('students.all');

        // 🔍 Student Details Route - ADD THIS LINE
        Route::get('/students/{id}/details', [PStudentController::class, 'getStudentDetails'])->name('students.details');
        // In your prefect routes group (around line 141-145)
Route::post('/violations/settle/{violation}', [PViolationController::class, 'settle'])->name('prefect.violations.settle');
        // 🔍 Live search routes
        Route::post('/students/search-parents', [PStudentController::class, 'searchParents'])->name('students.search-parents');
        Route::post('/students/search-advisers', [PStudentController::class, 'searchAdvisers'])->name('students.search-advisers');

        // Archive Routes
        Route::post('/students/archive', [PStudentController::class, 'archive'])->name('students.archive');
        Route::post('/students/mark-as-cleared', [PStudentController::class, 'markAsCleared'])->name('students.markAsCleared');
        Route::get('/students/archived', [PStudentController::class, 'getArchived'])->name('students.getArchived');
        Route::post('/students/restore', [PStudentController::class, 'restore'])->name('students.restore');
        Route::post('/students/destroy-multiple', [PStudentController::class, 'destroyMultiple'])->name('students.destroyMultiple');
        Route::post('/students/destroy-permanent', [PStudentController::class, 'destroyParentsPermanent'])->name('students.destroy.permanent');
         Route::get('/students/{student}/violations', [PStudentController::class, 'getStudentViolations'])->name('prefect.students.violations');
        Route::get('/students/{student}/violationsforstudent', [PStudentController::class, 'getStudentViolationsforStudent'])->name('prefect.students.violations');

        Route::get('/adviser', [PAdviserController::class, 'index'])->name('prefect.adviser');
        Route::get('/create/adviser', [PAdviserController::class, 'createAdviser'])->name('create.adviser');
        Route::post('/advisers/store', [PAdviserController::class, 'store'])->name('advisers.store');
        Route::put('/advisers/update', [PAdviserController::class, 'update'])->name('advisers.update');
        Route::get('/advisers/archived', [PAdviserController::class, 'getArchived'])->name('advisers.getArchived');
        Route::post('/advisers/move-to-trash', [PAdviserController::class, 'moveToTrash'])->name('advisers.move-to-trash');
        Route::post('/advisers/restore', [PAdviserController::class, 'restore'])->name('advisers.restore');
        Route::post('/advisers/destroy-multiple', [PAdviserController::class, 'destroyMultiple'])->name('advisers.destroyMultiple');
        Route::get('/advisers/all', [PStudentController::class, 'getAllAdvisers'])->name('advisers.all');

        // Parent Routes
        Route::get('/parentlists', [PParentController::class, 'parentlists'])->name('parent.lists');
        Route::get('/create/parent', [PParentController::class, 'createParent'])->name('create.parent');
        Route::post('/parents/store', [PParentController::class, 'parentStore'])->name('parents.store');
        Route::put('/parents/update', [PParentController::class, 'update'])->name('parents.update');

        // Parent Archive Routes
        Route::post('/parents/archive', [PParentController::class, 'archiveParents'])->name('parents.archive');
        Route::get('/parents/archived', [PParentController::class, 'getArchivedParents'])->name('parents.archived');
        Route::post('/parents/restore', [PParentController::class, 'restoreParents'])->name('parents.restore');
        Route::post('/parents/destroy-permanent', [PParentController::class, 'destroyParentsPermanent'])->name('parents.destroy.permanent');
        Route::get('/parents/archived/count', [PParentController::class, 'getArchivedParentsCount'])->name('parents.archived.count');

        // routes/api.php or web.php
        Route::get('/violations/generate-multiple-anecdotal-pdf', [PViolationController::class, 'generateMultipleAnecdotalPDF'])->name('prefect.generate-multiple-anecdotal-pdf');        // Violation Routes
        Route::get('/violation', [PViolationController::class, 'index'])->name('prefect.violation');
        Route::get('/violationAnecdotal', [PViolationController::class, 'indexAnecdotal'])->name('prefect.violationAnecdotal');
        Route::get('/violationAppointment', [PViolationController::class, 'indexAppointment'])->name('prefect.violationAppointment');
// Add this to your prefect routes
Route::post('/violations/get-sanction-stages', [PViolationController::class, 'getRecommendedSanctionStage'])->name('prefect.violations.get-sanction-stages');
Route::put('/violations/appointments/update-status', [PViolationAppointmentController::class, 'updateStatus'])
    ->name('prefect.appointments.updateStatus');
Route::post('/appointments/bulk-update-status', [PViolationAppointmentController::class, 'bulkUpdateStatus'])->name('prefect.appointments.bulkUpdateStatus');

        // In your routes file
        Route::post('/violation-appointments/store-multiple', [PViolationController::class, 'storeMultipleAppointments'])->name('prefect.storeMultipleAppointments');
        Route::post('/violations/store-multiple-anecdotals', [PViolationController::class, 'storeMultipleAnecdotals'])->name('prefect.storeMultipleAnecdotals');

        Route::get('/violations/creates', [PViolationController::class, 'create'])->name('violations.create');
        Route::post('/violations/store', [PViolationController::class, 'store'])->name('violations.store');
        Route::put('/violations/update/{violationId}', [PViolationController::class, 'update'])->name('violations.update');

// mao nia
Route::post('/violations/get-current-sanction', [PViolationController::class, 'getCurrentSanction'])
    ->name('prefect.violations.get-current-sanction');

// Route::get('/students/{studentId}/violations', [PStudentController::class, 'getStudentViolations']);



        // BAG O NI
        Route::post('/violations/get-sanctions', [PViolationController::class, 'getSanctionsByOffense'])->name('violations.get-sanctions');
// Add this route for offense history
Route::post('/violations/get-offense-history', [PViolationController::class, 'getOffenseHistory'])->name('prefect.violations.get-offense-history');
        // Change from POST to PUT
        Route::put('/violation-appointments/update/{appointmentId}', [PViolationController::class, 'updateAppointment'])->name('prefect.violation-appointments.update');
        Route::put('/violation-anecdotals/update/{anecdotalId}', [PViolationController::class, 'updateAnecdotal'])->name('prefect.violation-anecdotals.update');

        // Sanction Update Route
        Route::post('/violations/sanction/update', [PViolationController::class, 'updateSanction'])->name('prefect.updateSanction');

        // Violation Archive Routes
        Route::post('/violations/archive', [PViolationController::class, 'archive'])->name('violations.archive');
        Route::get('/violations/archived', [PViolationController::class, 'getArchived'])->name('violations.getArchived');
        Route::post('/violations/restore', [PViolationController::class, 'restore'])->name('violations.restore');
        Route::post('/violations/destroy-multiple', [PViolationController::class, 'destroyMultiple'])->name('violations.destroyMultiple');

        // Archive Routes for different types
        Route::post('/violation-appointments/archive', [PViolationController::class, 'archiveAppointments'])->name('violation.appointments.archive');
        Route::post('/violation-anecdotals/archive', [PViolationController::class, 'archiveAnecdotals'])->name('violation.anecdotals.archive');
        Route::get('/violation-appointments/archived', [PViolationController::class, 'getArchivedAppointments'])->name('violation.appointments.archived');
        Route::get('/violation-anecdotals/archived', [PViolationController::class, 'getArchivedAnecdotals'])->name('violation.anecdotals.archived');
        Route::post('/violations/restore-multiple', [PViolationController::class, 'restoreMultiple'])->name('violations.restore.multiple');
        Route::post('/violations/destroy-multiple-archived', [PViolationController::class, 'destroyMultipleArchived'])->name('violations.destroy.multiple.archived');
        Route::put('/violations/group/update', [PViolationController::class, 'updateGroup'])->name('prefect.violations.group.update');
        Route::post('/violations/destroy-permanent', [PViolationController::class, 'destroyPermanent'])->name('violations.destroy.permanent');
        // Violation AJAX Routes
        Route::post('/violations/search-students', [PViolationController::class, 'searchStudents'])->name('violations.search-students');
        Route::post('/violations/search-offenses', [PViolationController::class, 'searchOffenses'])->name('violations.search-offenses');

        // Updated settle routes
        Route::post('/violations/settle', [PViolationController::class, 'settleMultiple'])->name('prefect.violations.settle.multiple');
        Route::post('/violations/settle/{violation}', [PViolationController::class, 'settleSingle'])->name('prefect.violations.settle.single');

        Route::get('/offensesandsanctions', [POffenseSanctionController::class, 'index'])->name('offenses.sanctions');
        Route::get('/offenses/all', [POffenseSanctionController::class, 'getAllOffenses'])->name('offenses.all');
        Route::get('/sanctions/all', [POffenseSanctionController::class, 'getAllSanctions'])->name('sanctions.all');
        Route::get('/offensesandsanctions/details/{offenseType}', [POffenseSanctionController::class, 'getOffenseDetails'])
        ->name('prefect.offensesandsanctions.details');
        // In routes/web.php
        Route::get('/offensesandsanctions/details/{offenseType}', [POffenseSanctionController::class, 'getOffenseDetails'])
        ->where('offenseType', '.*')
        ->name('prefect.offensesandsanctions.details');
        Route::get('/offensesandsanctions/all-violations/{offenseType}', [POffenseSanctionController::class, 'getAllViolationsByOffense'])
        ->where('offenseType', '.*')
        ->name('prefect.offensesandsanctions.all-violations');
        Route::get('/sanctions/all', [POffenseSanctionController::class, 'getAllSanctionsForDropdown']);
        Route::get('/offensesandsanctions/sanctions-dropdown', [POffenseSanctionController::class, 'getAllSanctionsForDropdown']);
        Route::get('/offensesandsanctions/sanctions-by-offense/{offenseType}', [POffenseSanctionController::class, 'getSanctionsByOffense']);
    });
});






// ===================== New Adviser Routes =====================
Route::prefix('adviser')->group(function () {
    // Logout
    // Protected routes
    Route::middleware('auth:adviser')->group(function () {
        // Dashboard
        Route::get('/dashboard', [ADashboardController::class, 'dashboard'])->name('adviser.dashboard');

        // Notifications Route - ADD THIS LINE
        Route::get('/notifications', [ADashboardController::class, 'notifications'])->name('adviser.notifications');

        // Profile routes
        Route::post('/send-verification-code', [AProfileController::class, 'sendVerificationCode'])->name('adviser.send-verification-code');
        Route::post('/change-password', [AProfileController::class, 'changePassword'])->name('adviser.change-password');
        Route::get('/profile/info', [AProfileController::class, 'getProfileInfo'])->name('adviser.profile-info');
        Route::post('/profile/upload-image', [AProfileController::class, 'uploadProfileImage'])->name('adviser.upload-profile-image');
        Route::post('/profile/remove-image', [AProfileController::class, 'removeProfileImage'])->name('adviser.remove-profile-image');

        // Management Routes
        Route::get('/studentmanagement', [AStudentController::class, 'studentmanagement'])->name('adviser.student.management');
        Route::get('/parentlists', [AParentController::class, 'parentlists'])->name('adviser.parent.lists');
        Route::get('/offensesandsanctions', [AOffenseSanctionController::class, 'index'])->name('adviser.offenses.sanctions');


        // Student Routes
        Route::get('/create/student', [AStudentController::class, 'createStudent'])->name('adviser.create.student');
        Route::post('/students/store', [AStudentController::class, 'store'])->name('adviser.students.store');
        Route::put('/students/update', [AStudentController::class, 'update'])->name('adviser.students.update');
        Route::get('/students/search', [AStudentController::class, 'search'])->name('adviser.students.search');
        Route::get('/students/all', [AStudentController::class, 'getAllStudents'])->name('adviser.students.all');

        // 🔍 Student Details Route - ADD THIS LINE
        Route::get('/students/{id}/details', [AStudentController::class, 'getStudentDetails'])->name('adviser.students.details');
        // In your prefect routes group (around line 141-145)
Route::post('/violations/settle/{violation}', [AViolationController::class, 'settle'])->name('adviser.violations.settle');
        // 🔍 Live search routes
        Route::post('/students/search-parents', [AStudentController::class, 'searchParents'])->name('adviser.students.search-parents');
        Route::post('/students/search-advisers', [AStudentController::class, 'searchAdvisers'])->name('adviser.students.search-advisers');

        // Archive Routes
        Route::post('/students/archive', [AStudentController::class, 'archive'])->name('adviser.students.archive');
        Route::post('/students/mark-as-cleared', [AStudentController::class, 'markAsCleared'])->name('adviser.students.markAsCleared');
        Route::get('/students/archived', [AStudentController::class, 'getArchived'])->name('adviser.students.getArchived');
        Route::post('/students/restore', [AStudentController::class, 'restore'])->name('adviser.students.restore');
        Route::post('/students/destroy-multiple', [AStudentController::class, 'destroyMultiple'])->name('adviser.students.destroyMultiple');
        Route::post('/students/destroy-permanent', [AStudentController::class, 'destroyParentsPermanent'])->name('adviser.students.destroy.permanent');
         Route::get('/students/{student}/violations', [AStudentController::class, 'getStudentViolations'])->name('adviser.students.violations');
        Route::get('/students/{student}/violationsforstudent', [AStudentController::class, 'getStudentViolationsforStudent'])->name('adviser.students.violations');

        Route::get('/adviser', [AAdviserController::class, 'index'])->name('adviser.adviser');
        Route::get('/create/adviser', [AAdviserController::class, 'createAdviser'])->name('adviser.create.adviser');
        Route::post('/advisers/store', [AAdviserController::class, 'store'])->name('adviser.advisers.store');
        Route::put('/advisers/update', [AAdviserController::class, 'update'])->name('adviser.advisers.update');
        Route::get('/advisers/archived', [AAdviserController::class, 'getArchived'])->name('adviser.advisers.getArchived');
        Route::post('/advisers/move-to-trash', [AAdviserController::class, 'moveToTrash'])->name('adviser.advisers.move-to-trash');
        Route::post('/advisers/restore', [AAdviserController::class, 'restore'])->name('adviser.advisers.restore');
        Route::post('/advisers/destroy-multiple', [AAdviserController::class, 'destroyMultiple'])->name('adviser.advisers.destroyMultiple');
        Route::get('/advisers/all', [AStudentController::class, 'getAllAdvisers'])->name('adviser.advisers.all');

        // Parent Routes
        Route::get('/parentlists', [AParentController::class, 'parentlists'])->name('adviser.parent.lists');
        Route::get('/create/parent', [AParentController::class, 'createParent'])->name('adviser.create.parent');
        Route::post('/parents/store', [AParentController::class, 'parentStore'])->name('adviser.parents.store');
        Route::put('/parents/update', [AParentController::class, 'update'])->name('adviser.parents.update');

        // Parent Archive Routes
        Route::post('/parents/archive', [AParentController::class, 'archiveParents'])->name('adviser.parents.archive');
        Route::get('/parents/archived', [AParentController::class, 'getArchivedParents'])->name('adviser.parents.archived');
        Route::post('/parents/restore', [AParentController::class, 'restoreParents'])->name('adviser.parents.restore');
        Route::post('/parents/destroy-permanent', [AParentController::class, 'destroyParentsPermanent'])->name('adviser.parents.destroy.permanent');
        Route::get('/parents/archived/count', [AParentController::class, 'getArchivedParentsCount'])->name('adviser.parents.archived.count');

        // routes/api.php or web.php
        Route::get('/violations/generate-multiple-anecdotal-pdf', [AViolationController::class, 'generateMultipleAnecdotalPDF'])->name('adviser.generate-multiple-anecdotal-pdf');        // Violation Routes
        Route::get('/violation', [AViolationController::class, 'index'])->name('adviser.violation');
        Route::get('/violationAnecdotal', [AViolationController::class, 'indexAnecdotal'])->name('adviser.violationAnecdotal');
        Route::get('/violationAppointment', [AViolationController::class, 'indexAppointment'])->name('adviser.violationAppointment');
// Add this to your prefect routes
Route::post('/violations/get-sanction-stages', [AViolationController::class, 'getRecommendedSanctionStage'])->name('adviser.violations.get-sanction-stages');
Route::put('/violations/appointments/update-status', [AViolationAppointmentController::class, 'updateStatus'])
    ->name('adviser.appointments.updateStatus');
Route::post('/appointments/bulk-update-status', [AViolationAppointmentController::class, 'bulkUpdateStatus'])->name('adviser.appointments.bulkUpdateStatus');

        // In your routes file
        Route::post('/violation-appointments/store-multiple', [AViolationController::class, 'storeMultipleAppointments'])->name('adviser.storeMultipleAppointments');
        Route::post('/violations/store-multiple-anecdotals', [AViolationController::class, 'storeMultipleAnecdotals'])->name('adviser.storeMultipleAnecdotals');

        Route::get('/violations/creates', [AViolationController::class, 'create'])->name('adviser.violations.create');
        Route::post('/violations/store', [AViolationController::class, 'store'])->name('adviser.violations.store');
        Route::put('/violations/update/{violationId}', [AViolationController::class, 'update'])->name('adviser.violations.update');

// mao nia
Route::post('/violations/get-current-sanction', [AViolationController::class, 'getCurrentSanction'])
    ->name('adviser.violations.get-current-sanction');

// Route::get('/students/{studentId}/violations', [AStudentController::class, 'getStudentViolations']);



        // BAG O NI
        Route::post('/violations/get-sanctions', [AViolationController::class, 'getSanctionsByOffense'])->name('adviser.violations.get-sanctions');
// Add this route for offense history
Route::post('/violations/get-offense-history', [AViolationController::class, 'getOffenseHistory'])->name('adviser.violations.get-offense-history');
        // Change from POST to PUT
        Route::put('/violation-appointments/update/{appointmentId}', [AViolationController::class, 'updateAppointment'])->name('adviser.violation-appointments.update');
        Route::put('/violation-anecdotals/update/{anecdotalId}', [AViolationController::class, 'updateAnecdotal'])->name('adviser.violation-anecdotals.update');

        // Sanction Update Route
        Route::post('/violations/sanction/update', [AViolationController::class, 'updateSanction'])->name('adviser.updateSanction');

        // Violation Archive Routes
        Route::post('/violations/archive', [AViolationController::class, 'archive'])->name('adviser.violations.archive');
        Route::get('/violations/archived', [AViolationController::class, 'getArchived'])->name('adviser.violations.getArchived');
        Route::post('/violations/restore', [AViolationController::class, 'restore'])->name('adviser.violations.restore');
        Route::post('/violations/destroy-multiple', [AViolationController::class, 'destroyMultiple'])->name('adviser.violations.destroyMultiple');

        // Archive Routes for different types
        Route::post('/violation-appointments/archive', [AViolationController::class, 'archiveAppointments'])->name('adviser.violation.appointments.archive');
        Route::post('/violation-anecdotals/archive', [AViolationController::class, 'archiveAnecdotals'])->name('adviser.violation.anecdotals.archive');
        Route::get('/violation-appointments/archived', [AViolationController::class, 'getArchivedAppointments'])->name('adviser.violation.appointments.archived');
        Route::get('/violation-anecdotals/archived', [AViolationController::class, 'getArchivedAnecdotals'])->name('adviser.violation.anecdotals.archived');
        Route::post('/violations/restore-multiple', [AViolationController::class, 'restoreMultiple'])->name('adviser.violations.restore.multiple');
        Route::post('/violations/destroy-multiple-archived', [AViolationController::class, 'destroyMultipleArchived'])->name('adviser.violations.destroy.multiple.archived');
        Route::put('/violations/group/update', [AViolationController::class, 'updateGroup'])->name('adviser.violations.group.update');
        Route::post('/violations/destroy-permanent', [AViolationController::class, 'destroyPermanent'])->name('adviser.violations.destroy.permanent');
        // Violation AJAX Routes
        Route::post('/violations/search-students', [AViolationController::class, 'searchStudents'])->name('adviser.violations.search-students');
        Route::post('/violations/search-offenses', [AViolationController::class, 'searchOffenses'])->name('adviser.violations.search-offenses');

        // Updated settle routes
        Route::post('/violations/settle', [AViolationController::class, 'settleMultiple'])->name('adviser.violations.settle.multiple');
        Route::post('/violations/settle/{violation}', [AViolationController::class, 'settleSingle'])->name('adviser.violations.settle.single');

Route::post('/violations/refer-to-prefect', [AViolationController::class, 'referToPrefect'])
    ->name('adviser.violations.referToPrefect');



        Route::get('/offensesandsanctions', [AOffenseSanctionController::class, 'index'])->name('adviser.offenses.sanctions');
        Route::get('/offenses/all', [AOffenseSanctionController::class, 'getAllOffenses'])->name('adviser.offenses.all');
        Route::get('/sanctions/all', [AOffenseSanctionController::class, 'getAllSanctions'])->name('adviser.sanctions.all');
        Route::get('/offensesandsanctions/details/{offenseType}', [AOffenseSanctionController::class, 'getOffenseDetails'])
        ->name('prefect.offensesandsanctions.details');
        // In routes/web.php
        Route::get('/offensesandsanctions/details/{offenseType}', [AOffenseSanctionController::class, 'getOffenseDetails'])
        ->where('offenseType', '.*')
        ->name('prefect.offensesandsanctions.details');
        Route::get('/offensesandsanctions/all-violations/{offenseType}', [AOffenseSanctionController::class, 'getAllViolationsByOffense'])
        ->where('offenseType', '.*')
        ->name('prefect.offensesandsanctions.all-violations');
        Route::get('/sanctions/all', [AOffenseSanctionController::class, 'getAllSanctionsForDropdown']);
        Route::get('/offensesandsanctions/sanctions-dropdown', [AOffenseSanctionController::class, 'getAllSanctionsForDropdown']);
        Route::get('/offensesandsanctions/sanctions-by-offense/{offenseType}', [AOffenseSanctionController::class, 'getSanctionsByOffense']);
    });
});




// // ===================== Adviser Routes =====================
// Route::prefix('adviser')->group(function () {
//     // Logout
//     Route::post('/logout', [ALogoutController::class, 'logout'])->name('adviser.logout');

//     // Protected routes
//     Route::middleware('auth:adviser')->group(function () {
//         // Dashboard
//         Route::get('/dashboard', [ADashboardController::class, 'dashboard'])->name('adviser.dashboard');
//         Route::post('/send-verification-code', [AProfileController::class, 'sendVerificationCode'])->name('adviser.send-verification-code');
//         Route::post('/change-password', [AProfileController::class, 'changePassword'])->name('adviser.change-password');
//         Route::get('/profile-info', [AProfileController::class, 'getProfileInfo'])->name('adviser.profile-info');
//         Route::post('/upload-profile-image', [AProfileController::class, 'uploadProfileImage'])->name('adviser.upload-profile-image');
//         Route::post('/remove-profile-image', [AProfileController::class, 'removeProfileImage'])->name('adviser.remove-profile-image');


//         Route::get('/studentlists', [AStudentController::class, 'studentlists'])->name('student.list');
//         Route::get('/parentlist', [AParentController::class, 'parentlist'])->name('parent.list');
//         Route::get('/offensesanction', [AOffenseSanctionController::class, 'offensesanction'])->name('offense.sanction');


//         // Student Routes
//         Route::put('/students/update/{id}', [PStudentController::class, 'update'])->name('students.update');
//         Route::post('/students/mark-cleared', [AStudentController::class, 'markAsCleared'])->name('adviser.students.markCleared');



//         // Adviser Profile routes
//         Route::post('/send-verification-code', [AProfileController::class, 'sendVerificationCode'])->name('adviser.send-verification-code');
//         Route::post('/change-password', [AProfileController::class, 'changePassword'])->name('adviser.change-password');
//         Route::get('/profile-info', [AProfileController::class, 'getProfileInfo'])->name('adviser.profile-info');
//         Route::post('/upload-profile-image', [AProfileController::class, 'uploadProfileImage'])->name('adviser.upload-profile-image');
//         Route::post('/remove-profile-image', [AProfileController::class, 'removeProfileImage'])->name('adviser.remove-profile-image');

//         // Parent Routes
//         Route::get('/parents', [AParentController::class, 'parentlist'])->name('adviser.parentlist');
//         Route::get('/parents/create', [AParentController::class, 'createParent'])->name('adviser.create.parent');
//         Route::post('/parents/store', [AParentController::class, 'parentStore'])->name('adviser.parents.store');
//         Route::post('/parents/update/{id}', [AParentController::class, 'parentUpdate'])->name('adviser.parents.update');
//         Route::post('/parents/update/{id}', [AParentController::class, 'parentUpdatePost'])->name('adviser.parents.update');
//         Route::post('/parents/archive', [AParentController::class, 'archiveParents'])->name('adviser.parents.archive');
//         Route::get('/parents/archived', [AParentController::class, 'getArchivedParents'])->name('adviser.parents.archived');
//         Route::post('/parents/restore', [AParentController::class, 'restoreParents'])->name('adviser.parents.restore');
//         Route::post('/parents/destroy-permanent', [AParentController::class, 'destroyParentsPermanent'])->name('adviser.parents.destroy.permanent');
//         Route::get('/parents/archived/count', [AParentController::class, 'getArchivedParentsCount'])->name('adviser.parents.archived.count');
//         Route::get('/parents/{id}/details', [AParentController::class, 'getParentDetails'])->name('adviser.parents.details');
//         Route::post('/parents/search', [AParentController::class, 'searchParents'])->name('adviser.parents.search');
//         Route::post('/adviser/parents/check-duplicate', [AParentController::class, 'checkDuplicate'])->name('adviser.parents.check-duplicate');
//         Route::post('/parents/archived/search', [AParentController::class, 'searchArchivedParents'])->name('adviser.parents.archived.search');
//         Route::post('/parents/send-sms', [AParentController::class, 'sendSms'])->name('adviser.parents.send-sms');
//         Route::get('/parents/statistics', [AParentController::class, 'getParentStatistics'])->name('adviser.parents.statistics');
//         Route::get('/parents/{id}/students', [AParentController::class, 'getParentStudents'])->name('adviser.parents.students');

//         // Student Routes
//         Route::get('/create/student', [AStudentController::class, 'createStudent'])->name('adviser.create.student');
//         Route::post('/students/store', [AStudentController::class, 'store'])->name('adviser.students.store');
//         Route::post('/students/update/{id}', [AStudentController::class, 'updatePost'])->name('adviser.students.update');
//         Route::get('/students/search', [AStudentController::class, 'search'])->name('adviser.students.search');
//         Route::post('/adviser/students/check-duplicate', [AStudentController::class, 'checkDuplicate'])->name('adviser.students.check-duplicate');
//         // 🔍 Student Details Route for Adviser - ADD THIS LINE
//         Route::get('/students/{id}/parents', [AStudentController::class, 'getStudentParents'])->name('adviser.students.parents');
//         // 🔍 Live search routes
//         Route::post('/students/search-parents', [AStudentController::class, 'searchParents'])->name('adviser.students.search-parents');
//         Route::post('/students/search-advisers', [AStudentController::class, 'searchAdvisers'])->name('adviser.students.search-advisers');
//         Route::get('/students/{id}/details', [AStudentController::class, 'getStudentDetails'])->name('adviser.students.details');




//         // Archive Routes
//         Route::post('/students/archive', [AStudentController::class, 'archive'])->name('adviser.students.archive');
//         Route::post('/students/mark-as-cleared', [AStudentController::class, 'markAsCleared'])->name('adviser.students.markAsCleared');
//         Route::get('/students/archived', [AStudentController::class, 'getArchived'])->name('adviser.students.getArchived');
//         Route::post('/students/restore', [AStudentController::class, 'restore'])->name('adviser.students.restore');
//         Route::post('/students/destroy-multiple', [AStudentController::class, 'destroyMultiple'])->name('adviser.students.destroyMultiple');

//         // Report Routes
//         Route::get('/adviserreports', [AReportController::class, 'reports'])->name('adviser.reports');
//         Route::get('/reports/data/{reportId}', [AReportController::class, 'getReportData'])->name('adviser.reports.data');




//         // routes/api.php or web.php
//         Route::get('/violations/generate-multiple-anecdotal-pdf', [AViolationController::class, 'generateMultipleAnecdotalPDF'])->name('adviser.generate-multiple-anecdotal-pdf');        // Violation Routes
//         Route::get('/violation', [AViolationController::class, 'index'])->name('adviser.violation');
//         Route::get('/violationAnecdotal', [AViolationController::class, 'indexAnecdotal'])->name('adviser.violationAnecdotal');
//         Route::get('/violationAppointment', [AViolationController::class, 'indexAppointment'])->name('adviser.violationAppointment');

//         // In your routes file
//         Route::post('/violation-appointments/store-multiple', [AViolationController::class, 'storeMultipleAppointments'])->name('adviser.storeMultipleAppointments');
//         Route::post('/violations/store-multiple-anecdotals', [AViolationController::class, 'storeMultipleAnecdotals'])->name('adviser.storeMultipleAnecdotals');

//         Route::get('/violations/creates', [AViolationController::class, 'create'])->name('adviser.violations.create');
//         Route::post('/violations/store', [AViolationController::class, 'store'])->name('adviser.violations.store');
//         Route::put('/violations/update/{violationId}', [AViolationController::class, 'update'])->name('adviser.violations.update');

//         // BAG O NI
//         Route::post('/violations/get-offense-counts', [AViolationController::class, 'getOffenseCounts'])->name('adviser.violations.get-offense-counts');
//         Route::post('/violations/get-sanctions', [AViolationController::class, 'getSanctionsByOffense'])->name('adviser.violations.get-sanctions');
//         Route::post('/violations/refer-to-prefect', [AViolationController::class, 'referToPrefect'])->name('adviser.violations.refer-to-prefect');

//         // Change from POST to PUT
//         Route::put('/violation-appointments/update/{appointmentId}', [AViolationController::class, 'updateAppointment'])->name('adviser.violation-appointments.update');
//         Route::put('/violation-anecdotals/update/{anecdotalId}', [AViolationController::class, 'updateAnecdotal'])->name('adviser.violation-anecdotals.update');

//         // Violation Archive Routes
//         Route::post('/violations/archive', [AViolationController::class, 'archive'])->name('adviser.violations.archive');
//         Route::get('/violations/archived', [AViolationController::class, 'getArchived'])->name('adviser.violations.getArchived');
//         Route::post('/violations/restore', [AViolationController::class, 'restore'])->name('adviser.violations.restore');
//         Route::post('/violations/destroy-multiple', [AViolationController::class, 'destroyMultiple'])->name('adviser.violations.destroyMultiple');

//         // Archive Routes for different types
//         Route::post('/violation-appointments/archive', [AViolationController::class, 'archiveAppointments'])->name('adviser.violation.appointments.archive');
//         Route::post('/violation-anecdotals/archive', [AViolationController::class, 'archiveAnecdotals'])->name('adviser.violation.anecdotals.archive');
//         Route::get('/violation-appointments/archived', [AViolationController::class, 'getArchivedAppointments'])->name('adviser.violation.appointments.archived');
//         Route::get('/violation-anecdotals/archived', [AViolationController::class, 'getArchivedAnecdotals'])->name('adviser.violation.anecdotals.archived');
//         Route::post('/violations/restore-multiple', [AViolationController::class, 'restoreMultiple'])->name('adviser.violations.restore.multiple');
//         Route::post('/violations/destroy-multiple-archived', [AViolationController::class, 'destroyMultipleArchived'])->name('adviser.violations.destroy.multiple.archived');

//         // Violation AJAX Routes
//         Route::post('/violations/search-students', [AViolationController::class, 'searchStudents'])->name('adviser.violations.search-students');
//         Route::post('/violations/search-offenses', [AViolationController::class, 'searchOffenses'])->name('adviser.violations.search-offenses');


//     });
// });
