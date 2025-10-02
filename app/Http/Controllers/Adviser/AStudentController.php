<?php

namespace App\Http\Controllers\Adviser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth; // Assuming Prefect is authenticated
use App\Models\OffensesWithSanction;
use App\Models\ViolationRecord;
use App\Models\Adviser;
use App\Models\ViolationAppointment;



class AStudentController extends Controller
{

public function studentlist()
{
    // $adviserId = Auth::guard('adviser')->id();

    // // Active and Cleared students
    // $activeStudents = Student::where('adviser_id', $adviserId)
    //     ->whereIn('status', ['active', 'cleared'])
    //     ->with('parent')
    //     ->get();

    // // Archived students (inactive or completed)
    // $archivedStudents = Student::where('adviser_id', $adviserId)
    //     ->whereIn('status', ['inactive', 'completed'])
    //     ->with('parent')
    //     ->get();

    // return view('adviser.studentlist', compact('activeStudents', 'archivedStudents'));

    $students = Student::paginate(10);
        $sections = Adviser::select('adviser_section')->distinct()->pluck('adviser_section');


    // =========================
    // Summary Cards Data
    // =========================

    // Total students
    $totalStudents = Student::count();

    // Active students
    $activeStudents = Student::where('status', 'active')->count();

    // Completed students
    $completedStudents = Student::where('status', 'completed')->count();

    // Gender breakdown
    $maleStudents = Student::where('student_sex', 'male')->count();
    $femaleStudents = Student::where('student_sex', 'female')->count();
    $otherStudents = Student::where('student_sex', 'other')->count();

    // Violations today
    $violationsToday = ViolationRecord::whereDate('violation_date', now())->count();

    // Pending appointments
    $pendingAppointments = ViolationAppointment::where('violation_app_status', 'Pending')->count();

    // =========================
    // Pass data to Blade view
    // =========================
    return view('adviser.studentlist', compact(
        'students',
        'sections',
        'totalStudents',
         'activeStudents',
       'completedStudents',
        'maleStudents',
       'femaleStudents',
        'otherStudents',
        'violationsToday',
        'pendingAppointments'
    ));
}


}