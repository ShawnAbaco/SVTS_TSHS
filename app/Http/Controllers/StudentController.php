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

public function destroy($id)
{
    $student = Student::findOrFail($id);

    // Instead of deleting, mark as inactive
    $student->update([
        'status' => 'inactive',
    ]);

    return redirect()->route('student.list')->with('success', '🗑️ Student moved to archive successfully.');
}
public function restore($id)
{
    $student = Student::findOrFail($id);

    $student->update([
        'status' => 'active',
    ]);

    return redirect()->route('student.list')->with('success', '✅ Student restored successfully.');
}


public function archived()
{
    $archivedStudents = Student::where('status', 'inactive')
        ->leftJoin('tbl_parent', 'tbl_student.parent_id', '=', 'tbl_parent.parent_id')
        ->select(
            'tbl_student.student_id',
            'tbl_student.student_fname',
            'tbl_student.student_lname',
            'tbl_student.student_sex',
            'tbl_parent.parent_fname',
            'tbl_parent.parent_lname'
        )
        ->get()
        ->map(function ($student) {
            $student->parent_name = trim($student->parent_fname . ' ' . $student->parent_lname);
            return $student;
        });

    return response()->json($archivedStudents);
}


}
