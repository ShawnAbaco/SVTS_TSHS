<?php

namespace App\Http\Controllers\Adviser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Offense;
use App\Models\Sanction;
use App\Models\OffenseWithSanctionStage;
use App\Models\ViolationRecord;
use App\Models\Adviser;
use App\Models\ViolationAppointment;
use App\Models\ParentModel;
use App\Models\Complaints;

class AStudentController extends Controller
{
    public function studentlist()
    {
        $adviserId = Auth::guard('adviser')->id();

        // Get students only for the logged-in adviser - sorted by newest created first
        $students = Student::where('adviser_id', $adviserId)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        // Get sections only for the logged-in adviser
        $sections = Adviser::where('adviser_id', $adviserId)
            ->select('adviser_section')
            ->distinct()
            ->pluck('adviser_section');

        // Summary Cards Data - filtered by logged-in adviser
        $totalStudents = Student::where('adviser_id', $adviserId)
            ->where('status', 'active')
            ->count();

        $activeStudents = $totalStudents;

        $completedStudents = Student::where('adviser_id', $adviserId)
            ->where('status', 'completed')
            ->count();

        $maleStudents = Student::where('adviser_id', $adviserId)
            ->where('student_sex', 'male')
            ->where('status', 'active')
            ->count();

        $femaleStudents = Student::where('adviser_id', $adviserId)
            ->where('student_sex', 'female')
            ->where('status', 'active')
            ->count();

        $otherStudents = Student::where('adviser_id', $adviserId)
            ->where('student_sex', 'other')
            ->where('status', 'active')
            ->count();

        // Grade level counts for the logged-in adviser
        $adviserGradeLevel = Adviser::where('adviser_id', $adviserId)
            ->value('adviser_gradelevel');

        $grade11Students = ($adviserGradeLevel == '11') ? $totalStudents : 0;
        $grade12Students = ($adviserGradeLevel == '12') ? $totalStudents : 0;

        // Violations and appointments for students under this adviser
        $studentIds = Student::where('adviser_id', $adviserId)->pluck('student_id');

        return view('adviser.studentlist', compact(
            'totalStudents',
            'grade11Students',
            'grade12Students',
            'students',
            'sections',
            'activeStudents',
            'completedStudents',
            'maleStudents',
            'femaleStudents',
            'otherStudents',
        ));
    }

    /**
     * Check for duplicate students before saving
     */
    public function checkDuplicate(Request $request)
    {
        try {
            $adviserId = Auth::guard('adviser')->id();
            $students = $request->input('students', []);
            $duplicates = [];

            foreach ($students as $index => $studentData) {
                $existingStudent = Student::where('student_fname', $studentData['student_fname'])
                    ->where('student_lname', $studentData['student_lname'])
                    ->where('student_birthdate', $studentData['student_birthdate'])
                    ->where('adviser_id', $adviserId)
                    ->first();

                if ($existingStudent) {
                    $duplicates[] = [
                        'index' => $studentData['index'],
                        'name' => $studentData['student_fname'] . ' ' . $studentData['student_lname'],
                        'birthdate' => $studentData['student_birthdate']
                    ];
                }
            }

            return response()->json([
                'has_duplicates' => !empty($duplicates),
                'duplicates' => $duplicates
            ]);
        } catch (\Exception $e) {
            Log::error('Error checking duplicates: ' . $e->getMessage());
            return response()->json([
                'has_duplicates' => false,
                'error' => 'Error checking duplicates'
            ], 500);
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

            return redirect()->route('adviser.studentlist')->with('success', $savedCount . ' student(s) saved successfully!');
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
        $parents = ParentModel::with('students')->get();
        $advisers = Adviser::all();
        $students = Student::with(['parent', 'adviser'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('adviser.create-student', compact('students', 'parents', 'advisers'));
    }

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
     * Get archived students (including graduated)
     */
    public function getArchived()
    {
        try {
            $archivedStudents = Student::whereIn('status', ['inactive', 'graduated'])
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

    /**
     * Update student (POST method for form submission)
     */
    public function updatePost(Request $request, $id)
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

        try {
            $student = Student::findOrFail($id);
            $student->update($request->all());

            return response()->json([
                'success' => true,
                'message' => '✅ Student updated successfully!'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating student: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error updating student: ' . $e->getMessage()
            ], 500);
        }
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
            $query = $request->input('query');

            Log::info('Parent search request received', [
                'query' => $query,
                'all_input' => $request->all(),
                'content_type' => $request->header('Content-Type')
            ]);

            if (!$query || strlen($query) < 2) {
                return response()->json([]);
            }

            $parents = \App\Models\ParentModel::where(function ($q) use ($query) {
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

    /**
     * Mark students as cleared/graduated
     */
    public function markAsCleared(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:tbl_student,student_id'
        ]);

        try {
            Student::whereIn('student_id', $request->student_ids)
                ->update(['status' => 'graduated']);

            return response()->json([
                'success' => true,
                'message' => count($request->student_ids) . ' student(s) marked as graduated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error marking students as graduated: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get student details with violations and complaints - UPDATED
     */
    public function getStudentDetails($id)
    {
        try {
            $student = Student::with(['parent', 'adviser'])
                ->where('student_id', $id)
                ->firstOrFail();

            // Get violations for this student
            $violations = ViolationRecord::where('violator_id', $id)
                ->with(['offense', 'sanction'])
                ->orderBy('violation_date', 'desc')
                ->get()
                ->map(function ($violation) {
                    return [
                        'title' => $violation->offense->offense_type ?? 'Unknown Offense',
                        'offense_type' => $violation->offense->category ?? 'N/A',
                        'sanction' => $violation->sanction->sanction_consequences ?? 'N/A',
                        'time' => $violation->violation_time,
                        'date' => $violation->violation_date,
                        'status' => $violation->status
                    ];
                });

            // Get complaints for this student
            $complaints = Complaints::where('complainant_id', $id)
                ->orWhere('respondent_id', $id)
                ->with(['offense', 'sanction'])
                ->orderBy('complaints_date', 'desc')
                ->get()
                ->map(function ($complaint) use ($id) {
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

    /**
     * Get student parents 
     */
    public function getStudentParents($id)
    {
        try {
            $student = Student::with('parent')->find($id);

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student not found.'
                ], 404);
            }

            $parents = [];

            // Check if the student has a parent assigned
            if ($student->parent) {
                $parent = $student->parent;

                // Add the parent with relationship type from parent_relationship field
                $parents[] = [
                    'parent_id' => $parent->parent_id,
                    'parent_fname' => $parent->parent_fname,
                    'parent_lname' => $parent->parent_lname,
                    'parent_contactinfo' => $parent->parent_contactinfo,
                    'parent_email' => $parent->parent_email,
                    'pivot' => [
                        'relationship_type' => $parent->parent_relationship ?? 'Guardian'
                    ]
                ];
            }

            return response()->json([
                'success' => true,
                'parents' => $parents
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching student parents: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error fetching student parents'
            ], 500);
        }
    }
}
