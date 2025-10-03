<?php

namespace App\Http\Controllers\Prefect;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Adviser;
use App\Models\ParentModel;
use App\Models\ViolationRecord;
use App\Models\ViolationAppointment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PStudentController extends Controller
{


    public function createStudent(Request $request){
        $parents = ParentModel::with('students')->get();
        $advisers = Adviser::all();
        $students = Student::with(['parent', 'adviser'])
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('prefect.create-student', compact('students', 'parents','advisers'));
    }

    public function studentmanagement()
    {

        $totalStudents = DB::table('tbl_student')->count();

        // Grade 11 students (join adviser to check gradelevel)
        $grade11Students = DB::table('tbl_student')
            ->join('tbl_adviser', 'tbl_student.adviser_id', '=', 'tbl_adviser.adviser_id')
            ->where('tbl_adviser.adviser_gradelevel', '11')
            ->count();

        // Grade 12 students
        $grade12Students = DB::table('tbl_student')
            ->join('tbl_adviser', 'tbl_student.adviser_id', '=', 'tbl_adviser.adviser_id')
            ->where('tbl_adviser.adviser_gradelevel', '12')
            ->count();

        // Only show active students in main table
        $students = Student::where('status', 'active')->paginate(10);
        $sections = Adviser::select('adviser_section')->distinct()->pluck('adviser_section');

        // Summary Cards Data
        $totalStudents = Student::where('status', 'active')->count();
        $activeStudents = Student::where('status', 'active')->count();
        $completedStudents = Student::where('status', 'completed')->count();
        $maleStudents = Student::where('student_sex', 'male')->where('status', 'active')->count();
        $femaleStudents = Student::where('student_sex', 'female')->where('status', 'active')->count();
        $otherStudents = Student::where('student_sex', 'other')->where('status', 'active')->count();
        $violationsToday = ViolationRecord::whereDate('violation_date', now())->count();
        $pendingAppointments = ViolationAppointment::where('violation_app_status', 'Pending')->count();

        return view('prefect.student', compact('totalStudents','grade11Students','grade12Students',
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
     * Archive students (move to inactive status)
     */
    public function archive(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:tbl_student,student_id'
        ]);

        try {
            Student::whereIn('student_id', $request->student_ids)
                   ->update(['status' => 'inactive']);

            return response()->json([
                'success' => true,
                'message' => count($request->student_ids) . ' student(s) archived successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error archiving students: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get archived students
     */
    public function getArchived()
    {
        try {
            $archivedStudents = Student::where('status', 'inactive')
                                      ->orderBy('updated_at', 'desc')
                                      ->get();

            return response()->json($archivedStudents);
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }

    /**
     * Restore archived students
     */
    public function restore(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:tbl_student,student_id'
        ]);

        try {
            Student::whereIn('student_id', $request->student_ids)
                   ->update(['status' => 'active']);

            return response()->json([
                'success' => true,
                'message' => count($request->student_ids) . ' student(s) restored successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error restoring students: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Permanently delete multiple students
     */
    public function destroyMultiple(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:tbl_student,student_id'
        ]);

        try {
            Student::whereIn('student_id', $request->student_ids)->delete();

            return response()->json([
                'success' => true,
                'message' => count($request->student_ids) . ' student(s) deleted permanently'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting students: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
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
        $student->update($request->all());

        return redirect()->back()->with('success', '✅ Student updated successfully!');
    }

    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        $student->delete();

        return redirect()->back()->with('success', 'Student deleted permanently!');
    }

public function searchParents(Request $request)
{
    try {
        // Get query from either JSON or FormData
        $query = $request->input('query');

        Log::info('Parent search request received', [
            'query' => $query,
            'all_input' => $request->all(),
            'content_type' => $request->header('Content-Type')
        ]);

        if (!$query || strlen($query) < 2) {
            return response()->json([]);
        }

        // Make sure you're using the correct model name
        // If your model is named 'Parent', you might need to use the full namespace
        $parents = \App\Models\ParentModel::where(function($q) use ($query) {
                $q->where('parent_fname', 'LIKE', "%{$query}%")
                  ->orWhere('parent_lname', 'LIKE', "%{$query}%")
                  ->orWhereRaw("CONCAT(parent_fname, ' ', parent_lname) LIKE ?", ["%{$query}%"]);
            })
            ->where('status', 'active')
            ->limit(10)
            ->get(['parent_id', 'parent_fname', 'parent_lname']);

        Log::info('Parent search results', [
            'query' => $query,
            'results_count' => $parents->count()
        ]);

        return response()->json($parents);

    } catch (\Exception $e) {
        Log::error('Parent search error: ' . $e->getMessage());
        Log::error('Parent search stack trace: ' . $e->getTraceAsString());
        return response()->json([], 500);
    }
}

public function searchAdvisers(Request $request)
{
    $query = $request->input('query');

    $advisers = Adviser::where('adviser_fname', 'LIKE', "%{$query}%")
        ->orWhere('adviser_lname', 'LIKE', "%{$query}%")
        ->orWhereRaw("CONCAT(adviser_fname, ' ', adviser_lname) LIKE ?", ["%{$query}%"])
        ->where('status', 'active')
        ->limit(10)
        ->get(['adviser_id', 'adviser_fname', 'adviser_lname']);

    return response()->json($advisers);
}
}
