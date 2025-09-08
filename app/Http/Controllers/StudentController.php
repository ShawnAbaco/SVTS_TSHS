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
    $request->validate([
        'student_fname' => 'required|string',
        'student_lname' => 'required|string',
        'student_birthdate' => 'required|date',
        'student_address' => 'required|string',
        'student_contactinfo' => 'required|string',
        'parent_id' => 'required|exists:tbl_parent,parent_id',
    ]);

    // Assign adviser_id along with other fields
    $student = Student::create([
        'parent_id' => $request->parent_id,
        'student_fname' => $request->student_fname,
        'student_lname' => $request->student_lname,
        'student_birthdate' => $request->student_birthdate,
        'student_address' => $request->student_address,
        'student_contactinfo' => $request->student_contactinfo,
        'adviser_id' => auth()->guard('adviser')->id(), // <-- add adviser_id here
    ]);

    return redirect()->back()->with('success', 'Student created successfully!');
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
