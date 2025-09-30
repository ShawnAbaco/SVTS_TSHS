<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\Adviser;
use App\Models\Student;
use App\Models\OffensesWithSanction;
use App\Models\ParentModel;
use App\Models\ViolationRecord;
use App\Models\ViolationAppointment;
use App\Models\ViolationAnecdotal;
use App\Models\ComplaintsAppointment;
use App\Models\ComplaintsAnecdotal;
use Illuminate\Support\Facades\DB;


use App\Models\Complaints;

class PrefectController extends Controller
{
    public function showLoginForm()
    {
        return view('prefect.login');
    }

    public function logout(Request $request)
{
    Auth::guard('prefect')->logout();

    // Optionally invalidate the session
    $request->session()->invalidate();
    $request->session()->regenerateToken();

     return response()->json([
                'success' => true,
                'message' => 'Logout successful!',
                'redirect' => route('adviser.login')
            ]);
}




    public function dashboard()
    {
        $advisers = Adviser::all(); // Fetch all advisers for the dashboard
        return view('prefect.dashboard', compact('advisers'));
    }

//     public function studentmanagement()
// {


//     // Get students with status active, inactive, or completed, including their adviser
//     // $students = Student::with('adviser')
//     //     ->whereIn('status', ['active' , 'completed'])
//     //     ->get();

//     // // Get unique sections from advisers
//     // $sections = Adviser::select('adviser_section')->distinct()->pluck('adviser_section');

//     // // Get archived students (status = 'Cleared') with their adviser
//     // $archivedStudents = Student::with('adviser')
//     //     ->where('status', 'Cleared')
//     //     ->get();

//     // return view('prefect.studentmanagement', compact('students', 'sections', 'archivedStudents'));
//     $students = Student::paginate(10); // or use ->get() if no pagination
// return view('prefect.student', compact('students'));

// }

public function studentmanagement()
{
    // =========================
    // Paginated Students
    // =========================
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
    return view('prefect.student', compact(
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


    public function destroy($studentId)
{
    $student = Student::findOrFail($studentId);
    $student->delete();

    return redirect()->route('student.management')->with('success', 'Student deleted successfully.');
}


     public function peoplecomplaints()
    {
    $complaints = Complaints::with([
        'complainant',
        'respondent',
        'offense'
    ])->get();

    return view('prefect.peoplecomplaints', compact('complaints'));
    }
    // Show the edit form
public function complaintEdit($id)
{
    $complaint = Complaints::findOrFail($id);
    $students = Student::all();
    $offenses = OffensesWithSanction::all();
    return view('prefect.editcomplaint', compact('complaint', 'students', 'offenses'));
}

// Handle update
public function complaintUpdate(Request $request, $id)
{
    $complaint = Complaints::findOrFail($id);

    $complaint->update([
        'complainant_id' => $request->complainant_id,
        'respondent_id' => $request->respondent_id,
        'offense_sanc_id' => $request->offense_sanc_id,
        'complaints_incident' => $request->complaints_incident,
        'complaints_date' => $request->complaints_date,
        'complaints_time' => $request->complaints_time,
    ]);

    return redirect()->route('people.complaints')->with('success', 'Complaint updated successfully.');
}

// Delete complaint
public function complaintDestroy($id)
{
    $complaint = Complaints::findOrFail($id);
    $complaint->delete();

    return redirect()->route('people.complaints')->with('success', 'Complaint deleted successfully.');
}






// DIRIIIIIIIIIIIIII






public function parentlists()
{
    $parents = ParentModel::paginate(10); // ✅ Use paginate for links()
    return view('prefect.parentlists', compact("parents"));
}





//////////////////////////////////////////////////








        public function parentCreate()
    {
        return view('admin.parents.create');
    }

    public function parentStore(Request $request)
    {
        ParentModel::create($request->all());
        return redirect()->route('parent.index')->with('success', 'Parent created successfully.');
    }

    public function parentEdit($id)
    {
        $parent = ParentModel::findOrFail($id);
        return view('admin.parents.edit', compact('parent'));
    }

    public function parentUpdate(Request $request, $id)
    {
        $parent = ParentModel::findOrFail($id);
        $parent->update($request->all());
        return redirect()->route('parent.index')->with('success', 'Parent updated successfully.');
    }

    public function parentDestroy($id)
    {
        $parent = ParentModel::findOrFail($id);
        $parent->delete();
        return redirect()->route('parent.index')->with('success', 'Parent deleted successfully.');
    }
    public function violationrecords()
    {
        $violations = ViolationRecord::with(['student.parent', 'student.adviser', 'offense'])->get();
return view('prefect.violationrecords', compact('violations'));

    }

public function violationappointments()
{
    // Fetch all violation appointments with related student, parent, and offense
    $violation_appointments = ViolationAppointment::with([
        'violation.student.parent',
        'violation.offense'
    ])->get();

    // Fetch all violation records to populate the "Create Appointment" dropdown
    $violations = ViolationRecord::with([
        'student',
        'offense'
    ])->get();

    return view('prefect.violationappointments', compact('violation_appointments', 'violations'));
}
public function storeViolationAppointment(Request $request)
{
    $request->validate([
        'violation_id' => 'required|exists:tbl_violation_record,violation_id',
        'date' => 'required|date',
        'time' => 'required',
        'status' => 'required|string',
    ]);

    ViolationAppointment::create([
        'violation_id' => $request->violation_id,
        'violation_app_date' => $request->date,
        'violation_app_time' => $request->time,
        'violation_app_status' => $request->status,
    ]);

    return redirect()->route('violation.appointments')
                     ->with('success', 'Appointment created successfully.');
}




    public function violationanecdotals()
    {

         $violation_anecdotals = ViolationAnecdotal::with([
        'violation.student.adviser',
        'violation.offense'
    ])->get();

    return view('prefect.violationanecdotals', compact('violation_anecdotals'));

    }
public function reportgenerate()
    {
        return view('prefect.reportgenerate');
    }


// adviserrrrr

   public function usermanagement()
{
    $advisers = Adviser::all(); // Fetch all advisers
    return view('prefect.usermanagement', compact('advisers'));
}


///////////////////



    public function complaintsappointments()
    {
    // Fetch all appointments with related complaint info
    $appointments = ComplaintsAppointment::with('complaint')->get();
    $complaints = Complaints::all(); // For the select dropdown
    return view('prefect.complaintsAppointments', compact('appointments', 'complaints'));
    }
    public function complaintAppointmentStore(Request $request)
{
    $request->validate([
        'complaints_id' => 'required|exists:tbl_complaints,complaints_id',
        'comp_app_date' => 'required|date',
        'comp_app_time' => 'required',
        'comp_app_status' => 'required|string'
    ]);

    ComplaintsAppointment::create([
        'complaints_id' => $request->complaints_id,
        'comp_app_date' => $request->comp_app_date,
        'comp_app_time' => $request->comp_app_time,
        'comp_app_status' => $request->comp_app_status
    ]);

    return redirect()->route('complaints.appointments')->with('success', 'Appointment created successfully.');
}

// Delete appointment
public function complaintAppointmentDestroy($id)
{
    $appointment = ComplaintsAppointment::findOrFail($id);
    $appointment->delete();
    return redirect()->route('complaints.appointments')->with('success', 'Appointment deleted successfully.');
}

     public function complaintsanecdotals()
    {
         // Eager load complaints with complainant and respondent for efficiency
        $complaint_anecdotals = ComplaintsAnecdotal::with([
            'complaint.complainant',
            'complaint.respondent'
        ])->get();

        return view('prefect.complaintsanecdotals', compact('complaint_anecdotals'));
    }
public function offensesandsanctions()
{
    // Get all offenses grouped by offense_type, offense_description, ordered by offense_sanc_id
    $offenses = DB::table('tbl_offenses_with_sanction')
        ->select(
            'offense_type',
            'offense_description',
            DB::raw('GROUP_CONCAT(sanction_consequences SEPARATOR ", ") as sanctions'),
            DB::raw('MIN(offense_sanc_id) as min_id') // Use the smallest ID for sorting
        )
        ->groupBy('offense_type', 'offense_description')
        ->orderBy('min_id', 'ASC') // Sort by the earliest offense_sanc_id
        ->get();

    return view('prefect.offensesandsanctions', compact('offenses'));
}

 public function createAdviser()
    {
        return view('prefect.create-adviser'); // Blade file
    }



    // public function createAdviser(Request $request)
    // {
    //     $request->validate([
    //         'email' => 'required|email|unique:advisers',
    //         'password' => 'required|min:6',
    //     ]);

    //     Adviser::create([
    //         'email' => $request->email,
    //         'password' => Hash::make($request->password),
    //     ]);

    //     return back()->with('success', 'Adviser created successfully.');
    // }

}
