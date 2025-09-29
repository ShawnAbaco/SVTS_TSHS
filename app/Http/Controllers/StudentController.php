<?php


namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Student;
use App\Models\Adviser;
use App\Models\ParentModel;
use Illuminate\Support\Facades\Log;



class StudentController extends Controller
{

    public function createStudent(Request $request){
    $parents = ParentModel::with('students')->get();
        $advisers = Adviser::all(); // Fetch all advisers for the dashboard

        $students = Student::with(['parent', 'adviser'])
                        ->orderBy('created_at', 'desc')
                        ->get();
                        
                        
    return view('prefect.create-student', compact('students', 'parents','advisers'));

}


 public function create()
    {
        // Get all parents and advisers for dropdowns
        $parents = ParentModel::where('status', 'active')
                            ->orderBy('parent_lname')
                            ->get();
        
        $advisers = Adviser::where('status', 'active')
                          ->orderBy('adviser_lname')
                          ->get();
        
        return view('students.create', compact('parents', 'advisers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'students' => 'required|array|min:1',
            'students.*.student_fname' => 'required|string|max:255',
            'students.*.student_lname' => 'required|string|max:255',
            'students.*.student_sex' => 'nullable|in:male,female,other',
            'students.*.student_birthdate' => 'required|date',
            'students.*.student_address' => 'required|string|max:255',
            'students.*.student_contactinfo' => 'required|string|max:255',
            'students.*.parent_id' => 'required|exists:tbl_parent,parent_id',
            'students.*.adviser_id' => 'required|exists:tbl_adviser,adviser_id',
            'students.*.status' => 'nullable|in:active,inactive,transferred,graduated'
        ]);

        DB::beginTransaction();
        
        try {
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
            
            DB::commit();
            
            return redirect()->route('students.index')
                           ->with('success', 'Students created successfully!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->with('error', 'Failed to create students: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student)
    {
        $student->load(['parent', 'adviser']);
        return view('students.show', compact('student'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Student $student)
    {
        $parents = ParentModel::where('status', 'active')
                            ->orderBy('parent_lname')
                            ->get();
        
        $advisers = Adviser::where('status', 'active')
                          ->orderBy('adviser_lname')
                          ->get();
        
        return view('students.edit', compact('student', 'parents', 'advisers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'student_fname' => 'required|string|max:255',
            'student_lname' => 'required|string|max:255',
            'student_sex' => 'nullable|in:male,female,other',
            'student_birthdate' => 'required|date',
            'student_address' => 'required|string|max:255',
            'student_contactinfo' => 'required|string|max:255',
            'parent_id' => 'required|exists:tbl_parent,parent_id',
            'adviser_id' => 'required|exists:tbl_adviser,adviser_id',
            'status' => 'required|in:active,inactive,transferred,graduated'
        ]);

        $student->update($validated);
        
        return redirect()->route('students.index')
                       ->with('success', 'Student updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        $student->delete();
        
        return redirect()->route('students.index')
                       ->with('success', 'Student deleted successfully!');
    }

    /**
     * Bulk delete students
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:tbl_student,student_id'
        ]);

        Student::whereIn('student_id', $request->student_ids)->delete();
        
        return response()->json(['success' => 'Selected students deleted successfully!']);
    }

    /**
     * Get students by parent (for AJAX requests)
     */
    public function getByParent($parentId)
    {
        $students = Student::where('parent_id', $parentId)
                          ->where('status', 'active')
                          ->get();
        
        return response()->json($students);
    }
// public function store(Request $request)
// {
//     // Validate incoming request
//     $validated = $request->validate([
//         'student_fname'       => 'required|string|max:255',
//         'student_lname'       => 'required|string|max:255',
//         'student_birthdate'   => 'required|date|before_or_equal:today',
//         'student_address'     => 'required|string|max:500',
//         'student_contactinfo' => 'required|digits:11',
//         'parent_id'           => 'required|exists:tbl_parent,parent_id',
//         'student_sex'         => 'required|in:Male,Female',
//         'status'              => 'required|in:active,inactive',
//     ]);

//     try {
//         // Create student record
//         $student = Student::create([
//             'parent_id'          => $validated['parent_id'],
//             'adviser_id'         => auth('adviser')->id(), // adviser ID from logged-in adviser
//             'student_fname'      => $validated['student_fname'],
//             'student_lname'      => $validated['student_lname'],
//             'student_birthdate'  => $validated['student_birthdate'],
//             'student_address'    => $validated['student_address'],
//             'student_contactinfo'=> $validated['student_contactinfo'],
//             'student_sex'        => $validated['student_sex'],
//             'status'             => $validated['status'],
//         ]);

//         return redirect()->back()->with('success', '✅ Student created successfully!');
//     } catch (\Exception $e) {
//         // Log the error for debugging
//         Log::error('Student creation failed: '.$e->getMessage());

//         return redirect()->back()->with('error', '❌ Failed to create student. Please try again.');
//     }
// }


//  public function parentsearch(Request $request)
//     {
//         $query = $request->get('query');

//         $parents = DB::table('tbl_parent')
//             ->select(
//                 'parent_id',
//                 DB::raw("CONCAT(parent_fname, ' ', parent_lname) as parent_name")
//             )
//             ->where(DB::raw("CONCAT(parent_fname, ' ', parent_lname)"), 'like', "%{$query}%")
//             ->get();

//         return response()->json($parents);
//     }

//     public function update(Request $request, $id)
//     {
//         $student = Student::findOrFail($id);
//         $student->update($request->all());
//         return redirect()->back()->with('success', 'Student updated successfully!');
//     }

//     public function trash($id)
// {
//     $student = Student::findOrFail($id);

//     // Instead of deleting, mark as inactive
//     $student->update([
//         'status' => 'inactive',
//     ]);

//     return redirect()->route('student.list')->with('success', '🗑️ Student moved to archive successfully.');
// }

// public function bulkUpdateStatus(Request $request)
// {
//     $ids = $request->input('ids'); // array of selected student IDs
//     $status = $request->input('status'); // "completed" or "inactive"

//     if (!$ids || !is_array($ids)) {
//         return response()->json([
//             'success' => false,
//             'message' => 'No students selected.'
//         ], 400);
//     }

//     Student::whereIn('student_id', $ids)->update([
//         'status' => $status,
//     ]);

//     return response()->json([
//         'success' => true,
//         'message' => count($ids) . " students updated to {$status}.",
//     ]);
// }

// public function forceDelete($id)
// {
//     $student = Student::findOrFail($id);
//     $student->delete(); // actually remove from DB
//     return response()->json([
//         'success' => true,
//         'message' => 'Student deleted successfully.'
//     ]);
// }

// public function restore($id)
// {
//     $student = Student::findOrFail($id);

//     $student->status = 'active';
//     $student->save();

//     return response()->json([
//         'success' => true,
//         'message' => 'Student restored successfully.'
//     ]);
// }


// // prefect
// public function bulkClearStatus(Request $request)
// {
//     $ids = $request->input('ids', []);
//     $status = $request->input('status', 'Cleared'); // default to Cleared

//     if (empty($ids)) {
//         return response()->json([
//             'success' => false,
//             'message' => 'No students selected.'
//         ]);
//     }

//     try {
//         // Update the status of all selected students
//         Student::whereIn('student_id', $ids)->update(['status' => $status]);

//         return response()->json([
//             'success' => true,
//             'message' => "Selected students have been marked as '{$status}'."
//         ]);
//     } catch (\Exception $e) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Failed to update student status: ' . $e->getMessage()
//         ]);
//     }
// }

// public function restoreStudents(Request $request)
// {
//     $ids = $request->input('ids', []); // array of student IDs

//     if (empty($ids)) {
//         return response()->json([
//             'success' => false,
//             'message' => 'No students selected.'
//         ], 400);
//     }

//     // Restore all students with status 'Cleared'
//     Student::whereIn('student_id', $ids)
//         ->where('status', 'Cleared')
//         ->update(['status' => 'active']);

//     return response()->json([
//         'success' => true,
//         'message' => 'Selected students restored successfully.'
//     ]);
// }


}
