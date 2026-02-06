<?php

namespace App\Http\Controllers\Prefect;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Adviser;
use App\Models\ParentModel;
use App\Models\ViolationRecord;
use App\Models\ViolationAppointment;
use App\Models\Complaints;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PStudentController extends Controller
{
     // Updated notification data helper method for Prefect ONLY
private function getNotificationData()
{
    // Get recent violations (last 24 hours) that were referred FROM ADVISER TO PRECEF
    $referredViolations = \App\Models\ViolationRecord::with(['student.adviser', 'offense'])
        ->where('handled_by', 'prefect')
        ->where('status', 'pending')
        ->where('created_at', '>=', \Carbon\Carbon::now()->subDays(1))
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function($violation) {
            $studentName = 'Unknown Student';
            $violationType = 'N/A';
            $adviserName = 'N/A';

            if ($violation->student) {
                $studentName = $violation->student->student_fname . ' ' . $violation->student->student_lname;

                // Get adviser name if available
                if ($violation->student->adviser) {
                    $adviserName = $violation->student->adviser->adviser_fname . ' ' . $violation->student->adviser->adviser_lname;
                }
            }

            if ($violation->offense) {
                $violationType = $violation->offense->offense_type ?? 'N/A';
            }

            return [
                'violation_id' => $violation->violation_id,
                'student_name' => $studentName,
                'violation_type' => $violationType,
                'adviser_name' => $adviserName,
                'incident' => $violation->violation_incident,
                'date' => \Carbon\Carbon::parse($violation->violation_date)->format('M d, Y'),
                'time' => \Carbon\Carbon::parse($violation->violation_time)->format('h:i A'),
                'created_at' => $violation->created_at,
                'handled_by' => $violation->handled_by,
                'status' => $violation->status
            ];
        });

    $referredViolationsCount = $referredViolations->count();

    // For prefect, we also want to show new direct violations (created by prefect)
    $newDirectViolations = \App\Models\ViolationRecord::with(['student.adviser', 'offense'])
        ->where('handled_by', 'prefect')
        ->where('status', 'in_progress') // Direct violations by prefect are usually in_progress
        ->where('created_at', '>=', \Carbon\Carbon::now()->subDays(1))
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function($violation) {
            $studentName = 'Unknown Student';
            $violationType = 'N/A';

            if ($violation->student) {
                $studentName = $violation->student->student_fname . ' ' . $violation->student->student_lname;
            }

            if ($violation->offense) {
                $violationType = $violation->offense->offense_type ?? 'N/A';
            }

            return [
                'violation_id' => $violation->violation_id,
                'student_name' => $studentName,
                'violation_type' => $violationType,
                'incident' => $violation->violation_incident,
                'date' => \Carbon\Carbon::parse($violation->violation_date)->format('M d, Y'),
                'time' => \Carbon\Carbon::parse($violation->violation_time)->format('h:i A'),
                'created_at' => $violation->created_at,
                'handled_by' => $violation->handled_by,
                'status' => $violation->status
            ];
        });

    $newDirectViolationsCount = $newDirectViolations->count();

    // Calculate total notifications
    $totalViolationsCount = $referredViolationsCount + $newDirectViolationsCount;

    // For prefect, we don't care about new students or parents
    $newStudentsCount = 0;
    $newParentsCount = 0;
    $newComplaintsCount = 0;

    $notificationCount = $totalViolationsCount + $newStudentsCount + $newParentsCount + $newComplaintsCount;

    return [
        'notificationCount' => $notificationCount,
        'referredViolationsCount' => $referredViolationsCount,
        'newDirectViolationsCount' => $newDirectViolationsCount,
        'totalViolationsCount' => $totalViolationsCount,
        'newStudentsCount' => $newStudentsCount,
        'newParentsCount' => $newParentsCount,
        'newComplaintsCount' => $newComplaintsCount,
        'referredViolations' => $referredViolations,
        'newDirectViolations' => $newDirectViolations,
'newViolations' => collect($referredViolations)->merge($newDirectViolations),
        'newStudents' => collect([]), // Empty for prefect
        'newComplaints' => collect([]) // Empty for prefect
    ];
}

    public function studentmanagement()
    {
        // Get notification data with detailed information
        $notificationData = $this->getNotificationData();

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

        // Only show active students in main table, sorted by latest created/updated first - CHANGED TO 4 PER PAGE
        $students = Student::with(['parent', 'adviser'])
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->orderBy('updated_at', 'desc')
            ->paginate(6); // Changed from 20 to 4 per page

        $advisers = Adviser::where('status', 'active')
            ->orderBy('adviser_gradelevel')
            ->orderBy('adviser_section')
            ->get();

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

         return view('prefect.student', array_merge(
        compact(
            'totalStudents',
            'grade11Students',
            'grade12Students',
            'students',
            'sections',
            'totalStudents',
            'activeStudents',
            'completedStudents',
            'maleStudents',
            'femaleStudents',
            'otherStudents',
            'violationsToday',
            'pendingAppointments',
            'advisers' // ADD THIS
        ),
        $notificationData
    ));
}

    // Add this new method to get student details with parent information
    public function getStudentDetails($id)
    {
        try {
            $student = Student::with(['parent', 'adviser'])
                ->where('student_id', $id)
                ->firstOrFail();

            // Get violations and complaints for this student
            $violations = ViolationRecord::where('violator_id', $id)
                ->with(['offense', 'sanction'])
                ->orderBy('violation_date', 'desc')
                ->get()
                ->map(function($violation) {
                    return [
                        'title' => $violation->offense->offense_type ?? 'Unknown Offense',
                        'offense_type' => $violation->offense->category ?? 'N/A',
                        'sanction' => $violation->sanction->sanction_consequences ?? 'N/A',
                        'time' => $violation->violation_time,
                        'date' => $violation->violation_date,
                        'status' => $violation->status
                    ];
                });

            $complaints = Complaints::where('complainant_id', $id)
                ->orWhere('respondent_id', $id)
                ->with(['offense', 'sanction'])
                ->orderBy('complaints_date', 'desc')
                ->get()
                ->map(function($complaint) use ($id) {
                    $complainant = $complaint->complainant_id == $id ? 'This Student' : 'Other';
                    $respondent = $complaint->respondent_id == $id ? 'This Student' : 'Other';

                    return [
                        'title' => $complaint->offense->offense_type ?? 'Unknown Complaint',
                        'complainant' => $complainant,
                        'respondent' => $respondent,
                        'details' => $complaint->complaints_incident,
                        'date' => $complaint->complaints_date,
                        'status' => $complaint->status
                    ];
                });

            return response()->json([
                'success' => true,
                'student' => $student,
                'parent' => $student->parent,
                'adviser' => $student->adviser,
                'violations' => $violations,
                'complaints' => $complaints
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found'
            ], 404);
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

    public function createStudent(Request $request)
    {
        // Get notification data with detailed information
        $notificationData = $this->getNotificationData();

        $parents = ParentModel::with('students')->get();
        $advisers = Adviser::all();
        $students = Student::with(['parent', 'adviser'])
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('prefect.create-student', array_merge(
            compact('students', 'parents','advisers'),
            $notificationData
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
     * Mark students as cleared and move to archive
     */
    public function markAsCleared(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:tbl_student,student_id'
        ]);

        try {
            Student::whereIn('student_id', $request->student_ids)
                   ->update(['status' => 'cleared']);

            return response()->json([
                'success' => true,
                'message' => count($request->student_ids) . ' student(s) marked as cleared and archived successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error marking students as cleared: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get archived students
     */
    public function getArchived()
    {
        try {
            $archivedStudents = Student::with(['parent', 'adviser'])
                                      ->where('status', 'inactive')
                                      ->orWhere('status', 'cleared')
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

    public function update(Request $request)
{
    $request->validate([
        'student_fname' => 'required|string|max:255',
        'student_lname' => 'required|string|max:255',
        'student_sex' => 'required|string|max:10',
        'student_birthdate' => 'required|date',
        'student_address' => 'required|string|max:255',
        'student_contactinfo' => 'required|string|max:20',
        'status' => 'required|string|max:20',
        'student_id' => 'required|exists:tbl_student,student_id', // Add this
        'adviser_id' => 'required|exists:tbl_adviser,adviser_id', // Make sure this is also required
    ]);

    $student = Student::findOrFail($request->student_id);
    $student->update($request->all());

    return response()->json([
        'success' => true,
        'message' => 'Student updated successfully!'
    ]);
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

    public function getAllStudents()
    {
        try {
            $students = Student::with(['adviser', 'parent'])
                ->where('status', 'active') // Only active students
                ->get()
                ->map(function ($student) {
                    return [
                        'student_id' => $student->student_id,
                        'student_fname' => $student->student_fname,
                        'student_lname' => $student->student_lname,
                        'student_sex' => $student->student_sex,
                        'student_birthdate' => $student->student_birthdate,
                        'student_address' => $student->student_address,
                        'student_contactinfo' => $student->student_contactinfo,
                        'status' => $student->status,
                        'adviser' => $student->adviser ? [
                            'adviser_fname' => $student->adviser->adviser_fname,
                            'adviser_lname' => $student->adviser->adviser_lname,
                            'adviser_gradelevel' => $student->adviser->adviser_gradelevel,
                            'adviser_section' => $student->adviser->adviser_section,
                        ] : null,
                        'parent' => $student->parent
                    ];
                });

            return response()->json($students);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching students: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getStudentViolationsforStudent($studentId)
{
    try {
        \Log::info('Fetching violations for student ID: ' . $studentId);

        // Get student info with grade and section
        $student = Student::with(['adviser'])
            ->where('student_id', $studentId)
            ->first();

        if (!$student) {
            \Log::error('Student not found: ' . $studentId);
            return response()->json([
                'success' => false,
                'message' => 'Student not found'
            ], 404);
        }

        // Get violations for the student
        $violations = ViolationRecord::where('violator_id', $studentId)
            ->with(['offense', 'sanction'])
            ->orderBy('violation_date', 'desc')
            ->get()
            ->map(function ($violation) {
                return [
                    'violation_id' => $violation->violation_id,
                    'date_committed' => $violation->violation_date,
                    'offense_type' => $violation->offense->offense_type ?? 'N/A',
                    'offense_category' => $violation->offense->offense_category ?? 'N/A',
                    'sanction_consequences' => $violation->sanction->sanction_consequences ?? 'Not assigned',
                    'sanctions' => $violation->sanction->sanction_description ?? 'No sanction details',
                    'status' => $violation->status,
                    'sanction_status' => $violation->sanction_status, // ADDED: Include sanction_status
                    'incident_description' => $violation->violation_incident ?? 'No description provided',
                    'sanction_start_at' => $violation->sanction_start_at,
                    'sanction_end_at' => $violation->sanction_end_at,
                    'updated_at' => $violation->updated_at
                ];
            });

        // Get counts
        $totalViolations = $violations->count();
        $pendingViolations = $violations->where('status', 'pending')->count();
        $resolvedViolations = $violations->where('status', 'resolved')->count();

        \Log::info('Found ' . $totalViolations . ' violations for student: ' . $studentId);

        return response()->json($violations);

    } catch (\Exception $e) {
        \Log::error('Student violations error: ' . $e->getMessage());
        \Log::error('Stack trace: ' . $e->getTraceAsString());

        return response()->json([
            'success' => false,
            'error' => 'Server error',
            'message' => 'An error occurred while fetching student violations'
        ], 500);
    }
}


      /**
 * Get violations for a specific student (for Top Violators modal)
 */
public function getStudentViolations($studentId)
{
    try {
        \Log::info('Fetching violations for student ID: ' . $studentId);

        // Get student info with grade and section
        $student = Student::with(['adviser'])
            ->where('student_id', $studentId)
            ->first();

        if (!$student) {
            \Log::error('Student not found: ' . $studentId);
            return response()->json([
                'success' => false,
                'message' => 'Student not found'
            ], 404);
        }

        // Get violations for the student
        $violations = ViolationRecord::where('violator_id', $studentId)
            ->with(['offense', 'sanction'])
            ->orderBy('violation_date', 'desc')
            ->get()
            ->map(function ($violation) {
                return [
                    'violation_id' => $violation->violation_id,
                    'violation_date' => $violation->violation_date,
                    'offense_type' => $violation->offense->offense_type ?? 'N/A',
                    'sanction' => $violation->sanction->sanction_consequences ?? 'Not assigned',
                    'status' => $violation->status,
                    'description' => $violation->violation_incident ?? 'No description provided'
                ];
            });

        // Get counts
        $totalViolations = $violations->count();
        $pendingViolations = $violations->where('status', 'pending')->count();
        $resolvedViolations = $violations->where('status', 'resolved')->count();

        \Log::info('Found ' . $totalViolations . ' violations for student: ' . $studentId);

        return response()->json([
            'success' => true,
            'student' => [
                'student_fname' => $student->student_fname,
                'student_lname' => $student->student_lname,
                'grade_level' => $student->adviser->adviser_gradelevel ?? 'N/A',
                'section' => $student->adviser->adviser_section ?? 'N/A'
            ],
            'violations' => $violations,
            'total_violations' => $totalViolations,
            'pending_violations' => $pendingViolations,
            'resolved_violations' => $resolvedViolations
        ]);

    } catch (\Exception $e) {
        \Log::error('Student violations error: ' . $e->getMessage());
        \Log::error('Stack trace: ' . $e->getTraceAsString());

        return response()->json([
            'success' => false,
            'error' => 'Server error',
            'message' => 'An error occurred while fetching student violations'
        ], 500);
    }
}
public function studentlist()
{
    // Get notification data
    $notificationData = $this->getNotificationData();

    // Get students with their adviser and parent information
    $students = Student::with(['adviser', 'parent'])
        ->orderBy('created_at', 'desc')
        ->orderBy('updated_at', 'desc')
        ->paginate(6);

    // Get all active advisers for the dropdown
    $advisers = Adviser::where('status', 'active')
        ->orderBy('adviser_gradelevel')
        ->orderBy('adviser_section')
        ->get();

    return view('prefect.studentlists', array_merge(
        compact('students', 'advisers'),
        $notificationData
    ));
}

public function getAllAdvisers()
{
    try {
        $advisers = Adviser::where('status', 'active')
            ->orderBy('adviser_gradelevel')
            ->orderBy('adviser_section')
            ->get(['adviser_id', 'adviser_fname', 'adviser_lname', 'adviser_gradelevel', 'adviser_section']);

        return response()->json($advisers);
    } catch (\Exception $e) {
        return response()->json([], 500);
    }
}

}
