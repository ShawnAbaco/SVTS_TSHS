<?php


namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Student;



class StudentController extends Controller
{
public function store(Request $request)
{
    // Validate incoming request
    $validated = $request->validate([
        'student_fname'      => 'required|string|max:255',
        'student_lname'      => 'required|string|max:255',
        'student_birthdate'  => 'required|date',
        'student_address'    => 'required|string|max:500',
        'student_contactinfo'=> 'required|string|max:50',
        'parent_id'          => 'required|exists:tbl_parent,parent_id',
        'student_sex'        => 'nullable|in:Male,Female,Other',
    ]);

    try {
        // Create student record
        $student = Student::create([
            'parent_id'        => $validated['parent_id'],
            'student_fname'    => $validated['student_fname'],
            'student_lname'    => $validated['student_lname'],
            'student_birthdate'=> $validated['student_birthdate'],
            'student_address'  => $validated['student_address'],
            'student_contactinfo'=> $validated['student_contactinfo'],
            'adviser_id'       => auth()->guard('adviser')->id(), // ensure adviser is authenticated
            'student_sex'      => $validated['student_sex'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Student created successfully!');
    } catch (\Exception $e) {
        // Log the error for debugging
        \Log::error('Student creation failed: '.$e->getMessage());

        return redirect()->back()->with('error', 'Failed to create student. Please try again.');
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

    public function destroy($id)
{
    $student = Student::findOrFail($id);
    $student->delete();

    return redirect()->route('student.list')->with('success', 'Student deleted successfully.');
}

}
