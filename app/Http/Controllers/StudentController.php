<?php


namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Student;
use Illuminate\Support\Facades\Log;



class StudentController extends Controller
{
public function store(Request $request)
{
    // Validate incoming request
    $validated = $request->validate([
        'student_fname'       => 'required|string|max:255',
        'student_lname'       => 'required|string|max:255',
        'student_birthdate'   => 'required|date|before_or_equal:today',
        'student_address'     => 'required|string|max:500',
        'student_contactinfo' => 'required|digits:11',
        'parent_id'           => 'required|exists:tbl_parent,parent_id',
        'student_sex'         => 'required|in:Male,Female',
        'status'              => 'required|in:active,inactive',
    ]);

    try {
        // Create student record
        $student = Student::create([
            'parent_id'          => $validated['parent_id'],
            'adviser_id'         => auth('adviser')->id(), // adviser ID from logged-in adviser
            'student_fname'      => $validated['student_fname'],
            'student_lname'      => $validated['student_lname'],
            'student_birthdate'  => $validated['student_birthdate'],
            'student_address'    => $validated['student_address'],
            'student_contactinfo'=> $validated['student_contactinfo'],
            'student_sex'        => $validated['student_sex'],
            'status'             => $validated['status'],
        ]);

        return redirect()->back()->with('success', '✅ Student created successfully!');
    } catch (\Exception $e) {
        // Log the error for debugging
        Log::error('Student creation failed: '.$e->getMessage());

        return redirect()->back()->with('error', '❌ Failed to create student. Please try again.');
    }
}


 public function parentsearch(Request $request)
    {
        $query = $request->get('query');

        $parents = DB::table('tbl_parent')
            ->select(
                'parent_id',
                DB::raw("CONCAT(parent_fname, ' ', parent_lname) as parent_name")
            )
            ->where(DB::raw("CONCAT(parent_fname, ' ', parent_lname)"), 'like', "%{$query}%")
            ->get();

        return response()->json($parents);
    }

    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);
        $student->update($request->all());
        return redirect()->back()->with('success', 'Student updated successfully!');
    }

    public function trash($id)
{
    $student = Student::findOrFail($id);

    // Instead of deleting, mark as inactive
    $student->update([
        'status' => 'inactive',
    ]);

    return redirect()->route('student.list')->with('success', '🗑️ Student moved to archive successfully.');
}

public function bulkUpdateStatus(Request $request)
{
    $ids = $request->input('ids'); // array of selected student IDs
    $status = $request->input('status'); // "completed" or "inactive"

    if (!$ids || !is_array($ids)) {
        return response()->json([
            'success' => false,
            'message' => 'No students selected.'
        ], 400);
    }

    Student::whereIn('student_id', $ids)->update([
        'status' => $status,
    ]);

    return response()->json([
        'success' => true,
        'message' => count($ids) . " students updated to {$status}.",
    ]);
}

public function forceDelete($id)
{
    $student = Student::findOrFail($id);
    $student->delete(); // actually remove from DB
    return response()->json([
        'success' => true,
        'message' => 'Student deleted successfully.'
    ]);
}

public function restore($id)
{
    $student = Student::findOrFail($id);

    $student->status = 'active';
    $student->save();

    return response()->json([
        'success' => true,
        'message' => 'Student restored successfully.'
    ]);
}


// prefect
public function bulkClearStatus(Request $request)
{
    $ids = $request->input('ids', []);
    $status = $request->input('status', 'Cleared'); // default to Cleared

    if (empty($ids)) {
        return response()->json([
            'success' => false,
            'message' => 'No students selected.'
        ]);
    }

    try {
        // Update the status of all selected students
        Student::whereIn('student_id', $ids)->update(['status' => $status]);

        return response()->json([
            'success' => true,
            'message' => "Selected students have been marked as '{$status}'."
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to update student status: ' . $e->getMessage()
        ]);
    }
}

public function restoreStudents(Request $request)
{
    $ids = $request->input('ids', []); // array of student IDs

    if (empty($ids)) {
        return response()->json([
            'success' => false,
            'message' => 'No students selected.'
        ], 400);
    }

    // Restore all students with status 'Cleared'
    Student::whereIn('student_id', $ids)
        ->where('status', 'Cleared')
        ->update(['status' => 'active']);

    return response()->json([
        'success' => true,
        'message' => 'Selected students restored successfully.'
    ]);
}


}
