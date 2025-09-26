<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\ParentModel;
use App\Models\ViolationRecord;
use App\Models\ViolationAppointment;
use App\Models\ViolationAnecdotal;
use App\Models\Complaints;
use App\Models\ComplaintsAppointment;
use App\Models\ComplaintsAnecdotal;
use App\Models\OffensesWithSanction;
use Illuminate\Support\Facades\DB;


class AdviserController extends Controller
{
    public function showLoginForm()
    {
        return view('adviser.login');
    }


    public function dashboard()
    {
        $adviserId = Auth::guard('adviser')->id();

        $studentsCount   = Student::where('adviser_id', $adviserId)->count();
        $violationsCount = ViolationRecord::whereHas('student', fn($q) => $q->where('adviser_id', $adviserId))->count();
        $complaintsCount = Complaints::whereHas('complainant', fn($q) => $q->where('adviser_id', $adviserId))
                                    ->orWhereHas('respondent', fn($q) => $q->where('adviser_id', $adviserId))
                                    ->count();

        return view('adviser.dashboard', compact('studentsCount','violationsCount','complaintsCount'));
    }
public function studentlist()
{
    $adviserId = Auth::guard('adviser')->id();

    // Active and Cleared students
    $activeStudents = Student::where('adviser_id', $adviserId)
        ->whereIn('status', ['active', 'cleared'])
        ->with('parent')
        ->get();

    // Archived students (inactive or completed)
    $archivedStudents = Student::where('adviser_id', $adviserId)
        ->whereIn('status', ['inactive', 'completed'])
        ->with('parent')
        ->get();

    return view('adviser.studentlist', compact('activeStudents', 'archivedStudents'));
}



    public function parentlist()
{
    // Get all parents with their students
    $parents = \App\Models\ParentModel::with('students')->get();

    return view('adviser.parentlist', compact('parents'));
}


// Display violation records
    public function violationrecord()
    {
        $adviserId = Auth::guard('adviser')->id();

        $violations = ViolationRecord::with(['student', 'offense'])
            ->whereHas('student', fn($q) => $q->where('adviser_id', $adviserId))
            ->get();

        $students = Student::where('adviser_id', $adviserId)->get();
        $offenses = OffensesWithSanction::all();

        return view('adviser.violationrecord', compact('violations', 'students', 'offenses'));
    }

   public function violationappointment()
{
    $adviserId = Auth::guard('adviser')->id();

    // Load appointments with the related violation and student, filtered by adviser
    $appointments = ViolationAppointment::with(['violation.student'])
        ->whereHas('violation.student', fn($q) => $q->where('adviser_id', $adviserId))
        ->get();

    // Load students of this adviser for the Create Appointment dropdown
    $students = Student::where('adviser_id', $adviserId)->get();

    return view('adviser.violationappointment', compact('appointments', 'students'));
}


    public function violationanecdotal()
    {
        $adviserId = Auth::guard('adviser')->id();
        $anecdotal = ViolationAnecdotal::with('violation.student')
            ->whereHas('violation.student', fn($q) => $q->where('adviser_id', $adviserId))
            ->get();

        $students = \App\Models\Student::with('violations')->where('adviser_id', $adviserId)->get();

        return view('adviser.violationanecdotal', compact('anecdotal', 'students'));
    }

public function complaintsall()
{
    $adviserId = Auth::guard('adviser')->id();

    $complaints = Complaints::with(['complainant','respondent','offense'])
        ->whereHas('complainant', fn($q) => $q->where('adviser_id', $adviserId))
        ->orWhereHas('respondent', fn($q) => $q->where('adviser_id', $adviserId))
        ->get();

    // Add these lines:
    $offenses = OffensesWithSanction::all();

    return view('adviser.complaintsall', compact('complaints', 'offenses'));
}


public function complaintsappointment()
{
    $adviserId = Auth::guard('adviser')->id();

    $comp_appointments = ComplaintsAppointment::with([
            'complaint.complainant',
            'complaint.respondent',
            'complaint.offense'
        ])
        ->where(function($query) use ($adviserId) {
            $query->whereHas('complaint.complainant', fn($q) => $q->where('adviser_id', $adviserId))
                  ->orWhereHas('complaint.respondent', fn($q) => $q->where('adviser_id', $adviserId));
        })
        ->get();

    return view('adviser.complaintsappointment', compact('comp_appointments'));
}



    public function complaintsanecdotal()
{
    $adviserId = Auth::guard('adviser')->id();

    $anecdotal = ComplaintsAnecdotal::with(['complaint.complainant','complaint.respondent'])
        ->where(function($query) use ($adviserId) {
            $query->whereHas('complaint.complainant', fn($q) => $q->where('adviser_id', $adviserId))
                  ->orWhereHas('complaint.respondent', fn($q) => $q->where('adviser_id', $adviserId));
        })
        ->get();

    return view('adviser.complaintsanecdotal', compact('anecdotal'));
}

public function offensesanction()
{
    $offenses = DB::table('tbl_offenses_with_sanction')
        ->select(
            'offense_type',
            'offense_description',
            DB::raw('GROUP_CONCAT(DISTINCT sanction_consequences ORDER BY group_number, stage_number SEPARATOR ", ") as sanctions')
        )
        ->groupBy('offense_type', 'offense_description')
        ->orderBy('offense_type')
        ->get();

    return view('adviser.offensesanction', compact('offenses'));
}

    public function reports()
    {

        $adviserId = Auth::guard('adviser')->id();
        $students = Student::where('adviser_id', $adviserId)->with('violations')->get();

        return view('adviser.reports', compact('students'));
    }

    public function profilesettings()
    {
        $adviser = Auth::guard('adviser')->user();
        return view('adviser.profilesettings', compact('adviser'));
    }

    public function logout(Request $request)
    {
        Auth::guard('adviser')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('adviser.login');
    }
    public function destroyStudent($id)
{
    try {
        $student = DB::table('tbl_student')->where('student_id', $id)->first();

        if (!$student) {
            return redirect()->back()->with('error', 'Student not found.');
        }

        DB::table('tbl_student')->where('student_id', $id)->delete();

        return redirect()->back()->with('success', 'Student deleted successfully.');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Error deleting student: ' . $e->getMessage());
    }
}

// LIVE SEARCH STUDENTS
public function studentSearch(Request $request)
{
    $query = $request->get('query');
    $students = Student::with('adviser')
        ->where('student_fname', 'like', "%{$query}%")
        ->orWhere('student_lname', 'like', "%{$query}%")
        ->get()
        ->map(function($s){
            return [
                'student_id' => $s->student_id,
                'name' => $s->student_fname . ' ' . $s->student_lname,
                'adviser_name' => $s->adviser->adviser_fname . ' ' . $s->adviser->adviser_lname,
            ];
        });
    return response()->json($students);
}

// LIVE SEARCH OFFENSES
// LIVE SEARCH OFFENSES
public function offensesearch(Request $request)
{
    $query = $request->get('query');

    $offenses = DB::table('tbl_offenses_with_sanction')
        ->select('offense_sanc_id', 'offense_type')
        ->where('offense_type', 'like', "%{$query}%")
        ->get();

    return response()->json($offenses);
}


}
