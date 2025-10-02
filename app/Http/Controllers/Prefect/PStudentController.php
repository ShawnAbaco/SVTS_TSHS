<?php

namespace App\Http\Controllers\Prefect;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Adviser;
use App\Models\ParentModel;

use App\Models\ViolationRecord;
use App\Models\ViolationAppointment;

class PStudentController extends Controller
{

     public function createStudent(Request $request){
    $parents = ParentModel::with('students')->get();
        $advisers = Adviser::all(); // Fetch all advisers for the dashboard

        $students = Student::with(['parent', 'adviser'])
                        ->orderBy('created_at', 'desc')
                        ->get();
                        
                        
    return view('prefect.create-student', compact('students', 'parents','advisers'));
     }

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
    /**
     * Store newly created students in storage.
     */
    public function store(Request $request)
    {
        // ✅ Validate all student fields
        $validated = $request->validate([
            'students' => 'required|array|min:1',
            'students.*.student_fname' => 'required|string|max:255',
            'students.*.student_lname' => 'required|string|max:255',
            'students.*.student_sex' => 'nullable|string|in:male,female,other',
            'students.*.student_birthdate' => 'required|date',
            'students.*.student_address' => 'required|string|max:255',
            'students.*.student_contactinfo' => 'required|string|max:50',
            'students.*.parent_id' => 'required|exists:tbl_parent,parent_id',
            'students.*.adviser_id' => 'required|exists:tbl_adviser,adviser_id',
            'students.*.status' => 'nullable|string|in:active,inactive,transferred,graduated',
        ]);

        // ✅ Store each student
        foreach ($validated['students'] as $studentData) {
            Student::create([
                'student_fname' => $studentData['student_fname'],
                'student_lname' => $studentData['student_lname'],
                'student_sex' => $studentData['student_sex'] ?? null,
                'student_birthdate' => $studentData['student_birthdate'],
                'student_address' => $studentData['student_address'],
                'student_contactinfo' => $studentData['student_contactinfo'],
                'parent_id' => $studentData['parent_id'],
                'adviser_id' => $studentData['adviser_id'],
                'status' => $studentData['status'] ?? 'active',
            ]);
        }

        return redirect()->route('student.management')->with('success', 'Students saved successfully!');

    }


       public function index()
    {
        $students = Student::paginate(10); // ✅ pagination for Blade
        return view('prefect.student', compact('students'));
    }

    /**
     * Show create form (optional)
     */
    // public function create()
    // {
    //     return view('prefect.student-create');
    // }

    /**
     * Store a new student
     */
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'student_fname' => 'required|string|max:255',
    //         'student_lname' => 'required|string|max:255',
    //         'student_sex' => 'required|string|max:10',
    //         'student_birthdate' => 'required|date',
    //         'student_address' => 'required|string|max:255',
    //         'student_contactinfo' => 'required|string|max:20',
    //         'status' => 'required|string|max:50',
    //     ]);

    //     Student::create([
    //         'student_fname' => $request->student_fname,
    //         'student_lname' => $request->student_lname,
    //         'student_sex' => $request->student_sex,
    //         'student_birthdate' => $request->student_birthdate,
    //         'student_address' => $request->student_address,
    //         'student_contactinfo' => $request->student_contactinfo,
    //         'status' => $request->status,
    //     ]);

    //     return redirect()->route('prefect.students')->with('success', 'Student created successfully!');
    // }

    /**
     * Update an existing student
     */

   public function update(Request $request, $id)
    {
        $request->validate([
            'student_fname' => 'required|string|max:255',
            'student_lname' => 'required|string|max:255',
            'student_sex' => 'required|string|max:10',
            'student_birthdate' => 'required|date',
            'student_address' => 'required|string|max:255',
            'student_contactinfo' => 'required|string|max:20',
            'status' => 'required|string|max:20',
        ]);

        $student = Student::findOrFail($id);

        $student->update([
            'student_fname' => $request->student_fname,
            'student_lname' => $request->student_lname,
            'student_sex' => $request->student_sex,
            'student_birthdate' => $request->student_birthdate,
            'student_address' => $request->student_address,
            'student_contactinfo' => $request->student_contactinfo,
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', '✅ Student updated successfully!');
    }
    /**
     * Soft delete or archive (optional)
     */
    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        $student->delete();

        return redirect()->back()->with('success', 'Student archived successfully!');
    }
}
