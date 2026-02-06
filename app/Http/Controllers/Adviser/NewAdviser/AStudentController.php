<?php

namespace App\Http\Controllers\Adviser\NewAdviser;

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
use Illuminate\Support\Facades\Auth;


class AStudentController extends Controller
{
    // UPDATED NOTIFICATION DATA HELPER METHOD WITH DETAILED INFORMATION
    private function getNotificationData()
    {
        // Get recent violations (last 24 hours) with details
        $newViolations = ViolationRecord::with(['student.adviser', 'offense'])
            ->where('status', 'pending')
            ->where('created_at', '>=', Carbon::now()->subDays(1))
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
                    'student_name' => $studentName,
                    'violation_type' => $violationType,
                    'date' => Carbon::parse($violation->violation_date)->format('M d, Y'),
                    'created_at' => $violation->created_at
                ];
            });

        $newViolationsCount = $newViolations->count();

        // Get recent students (last 24 hours) with details
        $newStudents = Student::with(['adviser'])
            ->where('status', 'active')
            ->where('created_at', '>=', Carbon::now()->subDays(1))
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($student) {
                $gradeLevel = 'N/A';
                $section = 'N/A';

                if ($student->adviser) {
                    $gradeLevel = $student->adviser->adviser_gradelevel ?? 'N/A';
                    $section = $student->adviser->adviser_section ?? 'N/A';
                }

                return [
                    'name' => $student->student_fname . ' ' . $student->student_lname,
                    'grade_level' => $gradeLevel,
                    'section' => $section,
                    'created_at' => $student->created_at
                ];
            });

        $newStudentsCount = $newStudents->count();



        $newParentsCount = 0; // You can update this when you have parent creation tracking

        $notificationCount = $newViolationsCount + $newStudentsCount + $newParentsCount ;

        return [
            'notificationCount' => $notificationCount,
            'newViolationsCount' => $newViolationsCount,
            'newStudentsCount' => $newStudentsCount,
            'newParentsCount' => $newParentsCount,

            'newViolations' => $newViolations,
            'newStudents' => $newStudents,
        ];
    }

    public function studentmanagement()
{
    // Get the logged-in adviser's ID
    $adviserId = Auth::guard('adviser')->id();

    // Get notification data with detailed information
    $notificationData = $this->getNotificationData();

    // Total students for this adviser
    $totalStudents = DB::table('tbl_student')
        ->where('adviser_id', $adviserId)
        ->count();

    // Grade 11 students for this adviser (join adviser to check gradelevel)
    $grade11Students = DB::table('tbl_student')
        ->join('tbl_adviser', 'tbl_student.adviser_id', '=', 'tbl_adviser.adviser_id')
        ->where('tbl_adviser.adviser_gradelevel', '11')
        ->where('tbl_student.adviser_id', $adviserId)
        ->count();

    // Grade 12 students for this adviser
    $grade12Students = DB::table('tbl_student')
        ->join('tbl_adviser', 'tbl_student.adviser_id', '=', 'tbl_adviser.adviser_id')
        ->where('tbl_adviser.adviser_gradelevel', '12')
        ->where('tbl_student.adviser_id', $adviserId)
        ->count();

    // Only show active students from this adviser's advisory
    $students = Student::with(['parent', 'adviser'])
        ->where('status', 'active')
        ->where('adviser_id', $adviserId)
        ->orderBy('created_at', 'desc')
        ->orderBy('updated_at', 'desc')
        ->paginate(6);

    // Get the logged-in adviser's details
    $adviser = Adviser::find($adviserId);

    // Get all advisers (or just the logged-in adviser) - you might want to change this
    $advisers = Adviser::where('status', 'active')
        ->where('adviser_id', $adviserId) // Only show the logged-in adviser
        ->orderBy('adviser_gradelevel')
        ->orderBy('adviser_section')
        ->get();

    // Get sections for the logged-in adviser only
    $sections = Adviser::select('adviser_section')
        ->where('adviser_id', $adviserId)
        ->distinct()
        ->pluck('adviser_section');

    // Summary Cards Data - Filter by adviser's students
    $totalStudents = Student::where('status', 'active')
        ->where('adviser_id', $adviserId)
        ->count();

    $activeStudents = Student::where('status', 'active')
        ->where('adviser_id', $adviserId)
        ->count();

    $completedStudents = Student::where('status', 'completed')
        ->where('adviser_id', $adviserId)
        ->count();

    $maleStudents = Student::where('student_sex', 'male')
        ->where('status', 'active')
        ->where('adviser_id', $adviserId)
        ->count();

    $femaleStudents = Student::where('student_sex', 'female')
        ->where('status', 'active')
        ->where('adviser_id', $adviserId)
        ->count();

    $otherStudents = Student::where('student_sex', 'other')
        ->where('status', 'active')
        ->where('adviser_id', $adviserId)
        ->count();

    // Violations today for adviser's students
    $violationsToday = ViolationRecord::whereDate('violation_date', now())
        ->whereHas('student', function($query) use ($adviserId) {
            $query->where('adviser_id', $adviserId);
        })
        ->count();

    // Pending appointments for adviser's students
    $pendingAppointments = ViolationAppointment::where('violation_app_status', 'Pending')
        ->whereHas('violation.student', function($query) use ($adviserId) {
            $query->where('adviser_id', $adviserId);
        })
        ->count();

    return view('adviser.NewAdviser.student', array_merge(
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
            'adviser', // Changed from advisers to adviser (single)
            'advisers' // Keep if needed for dropdowns
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
        // Get the logged-in adviser's ID
        $adviserId = Auth::guard('adviser')->id();

        $validated = $request->validate([
            'students' => 'required|array|min:1',
            'students.*.student_fname' => 'required|string|max:255',
            'students.*.student_lname' => 'required|string|max:255',
            'students.*.student_sex' => 'nullable|string|in:male,female,other',
            'students.*.student_birthdate' => 'required|date',
            'students.*.student_address' => 'required|string|max:255',
            'students.*.student_contactinfo' => 'required|string|max:50',
            'students.*.parent_id' => 'required|exists:tbl_parent,parent_id',
            'students.*.status' => 'nullable|string|in:active,inactive,transferred,graduated',
        ]);

        try {
            DB::beginTransaction();

            $duplicateStudents = [];
            $validStudents = [];
            $savedStudents = [];

            // Enhanced duplicate checking with transaction
            foreach ($validated['students'] as $index => $studentData) {
                $existingStudent = Student::where('student_fname', $studentData['student_fname'])
                    ->where('student_lname', $studentData['student_lname'])
                    ->where('student_birthdate', $studentData['student_birthdate'])
                    ->where('adviser_id', $adviserId)
                    ->first();

                if ($existingStudent) {
                    $duplicateStudents[] = [
                        'index' => $index + 1,
                        'name' => $studentData['student_fname'] . ' ' . $studentData['student_lname'],
                        'birthdate' => $studentData['student_birthdate']
                    ];
                } else {
                    $validStudents[] = [
                        'data' => $studentData,
                        'index' => $index
                    ];
                }
            }

            // If there are duplicates, return error
            if (!empty($duplicateStudents)) {
                DB::rollBack();

                $errorMessage = "The following students already exist in the database:\n";
                foreach ($duplicateStudents as $duplicate) {
                    $errorMessage .= "Student #{$duplicate['index']}: {$duplicate['name']} (Birthdate: {$duplicate['birthdate']})\n";
                }
                $errorMessage .= "\nPlease remove or modify these students before saving.";

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage,
                        'duplicates' => $duplicateStudents
                    ], 422);
                }

                return back()->with('error', $errorMessage);
            }

            // Save only non-duplicate students
            $savedCount = 0;
            foreach ($validStudents as $validStudent) {
                $studentData = $validStudent['data'];

                $student = Student::create([
                    'student_fname' => $studentData['student_fname'],
                    'student_lname' => $studentData['student_lname'],
                    'student_sex' => $studentData['student_sex'] ?? null,
                    'student_birthdate' => $studentData['student_birthdate'],
                    'student_address' => $studentData['student_address'],
                    'student_contactinfo' => $studentData['student_contactinfo'],
                    'parent_id' => $studentData['parent_id'],
                    'adviser_id' => $adviserId,
                    'status' => $studentData['status'] ?? 'active',
                ]);

                $savedStudents[] = $student;
                $savedCount++;
            }

            DB::commit();

            // Log successful creation
            Log::info("Adviser {$adviserId} created {$savedCount} students", [
                'adviser_id' => $adviserId,
                'student_count' => $savedCount,
                'student_ids' => array_map(function ($student) {
                    return $student->student_id;
                }, $savedStudents)
            ]);

            // Return JSON response for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $savedCount . ' student(s) saved successfully!',
                    'saved_count' => $savedCount
                ]);
            }

            return redirect()->route('adviser.student.management')->with('success', $savedCount . ' student(s) saved successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error saving students: ' . $e->getMessage());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while saving students: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'An error occurred while saving students.');
        }
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

        return view('adviser.NewAdviser.create-student', array_merge(
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
 * FILTERED: Only shows violations under the logged-in adviser's advisory
 */
public function getStudentViolations($studentId)
{
    try {
        \Log::info('Fetching violations for student ID: ' . $studentId);

        // Get the logged-in adviser's ID
        $adviserId = Auth::guard('adviser')->id();

        if (!$adviserId) {
            \Log::error('Adviser not authenticated');
            return response()->json([
                'success' => false,
                'message' => 'Adviser not authenticated'
            ], 401);
        }

        // Get student info with grade and section - ensure student belongs to this adviser
        $student = Student::with(['adviser'])
            ->where('student_id', $studentId)
            ->where('adviser_id', $adviserId) // ADDED: Filter by adviser_id
            ->first();

        if (!$student) {
            \Log::error('Student not found or not under your advisory: ' . $studentId);
            return response()->json([
                'success' => false,
                'message' => 'Student not found or not under your advisory'
            ], 404);
        }

        // Get violations for the student - FILTERED by adviser
        $violations = ViolationRecord::where('violator_id', $studentId)
            ->with(['offense', 'sanction'])
            ->whereHas('student', function($query) use ($adviserId) {
                $query->where('adviser_id', $adviserId); // ADDED: Filter by adviser_id
            })
            ->orderBy('violation_date', 'desc')
            ->get()
            ->map(function ($violation) {
                return [
                    'violation_id' => $violation->violation_id,
                    'violation_date' => $violation->violation_date,
                    'violation_time' => $violation->violation_time,
                    'offense_type' => $violation->offense->offense_type ?? 'N/A',
                    'sanction' => $violation->sanction->sanction_consequences ?? 'Not assigned',
                    'status' => $violation->status,
                    'sanction_status' => $violation->sanction_status ?? 'pending',
                    'description' => $violation->violation_incident ?? 'No description provided',
                    'handled_by' => $violation->handled_by ?? 'unknown'
                ];
            });

        // Get counts
        $totalViolations = $violations->count();
        $pendingViolations = $violations->where('status', 'pending')->count();
        $inProgressViolations = $violations->where('status', 'in_progress')->count();
        $resolvedViolations = $violations->where('status', 'resolved')->count();
        $dismissedViolations = $violations->where('status', 'dismissed')->count();
        $noncompliantViolations = $violations->where('status', 'noncompliant')->count();

        // Get handled_by statistics
        $adviserHandled = $violations->where('handled_by', 'adviser')->count();
        $prefectHandled = $violations->where('handled_by', 'prefect')->count();

        \Log::info('Found ' . $totalViolations . ' violations for student: ' . $studentId . ' under adviser: ' . $adviserId);

        return response()->json([
            'success' => true,
            'student' => [
                'student_id' => $student->student_id,
                'student_fname' => $student->student_fname,
                'student_lname' => $student->student_lname,
                'grade_level' => $student->adviser->adviser_gradelevel ?? 'N/A',
                'section' => $student->adviser->adviser_section ?? 'N/A',
                'adviser_name' => $student->adviser ? $student->adviser->adviser_fname . ' ' . $student->adviser->adviser_lname : 'N/A'
            ],
            'violations' => $violations,
            'statistics' => [
                'total_violations' => $totalViolations,
                'status_counts' => [
                    'pending' => $pendingViolations,
                    'in_progress' => $inProgressViolations,
                    'resolved' => $resolvedViolations,
                    'dismissed' => $dismissedViolations,
                    'noncompliant' => $noncompliantViolations
                ],
                'handled_by_counts' => [
                    'adviser' => $adviserHandled,
                    'prefect' => $prefectHandled
                ],
                'adviser_filter_applied' => true,
                'filtered_for_adviser_id' => $adviserId
            ]
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

    return view('adviser.NewAdviser.student', array_merge(
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
