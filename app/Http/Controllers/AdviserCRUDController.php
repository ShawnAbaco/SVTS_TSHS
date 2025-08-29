<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ViolationRecord;
use App\Models\ViolationAppointment;
use App\Models\ComplaintsAppointment;


class AdviserCRUDController extends Controller
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


public function parentStore(Request $request)
{
    $request->validate([
        'parent_fname' => 'required|string',
        'parent_lname' => 'required|string',
        'parent_birthdate' => 'required|date',
        'parent_contactinfo' => 'required|string',
    ]);

    DB::table('tbl_parent')->insert([
        'parent_fname' => $request->parent_fname,
        'parent_lname' => $request->parent_lname,
        'parent_birthdate' => $request->parent_birthdate,
        'parent_contactinfo' => $request->parent_contactinfo,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return redirect()->back()->with('success', 'Parent added successfully!');
}
public function parentUpdate(Request $request)
{
    $request->validate([
        'parent_id' => 'required|exists:tbl_parent,parent_id',
        'parent_fname' => 'required|string',
        'parent_lname' => 'required|string',
        'parent_birthdate' => 'required|date',
        'parent_contactinfo' => 'required|string',
    ]);

    DB::table('tbl_parent')->where('parent_id', $request->parent_id)->update([
        'parent_fname' => $request->parent_fname,
        'parent_lname' => $request->parent_lname,
        'parent_birthdate' => $request->parent_birthdate,
        'parent_contactinfo' => $request->parent_contactinfo,
        'updated_at' => now(),
    ]);

    return redirect()->back()->with('success', 'Parent updated successfully!');
}

public function destroyParent($id)
{
    try {
        $parent = DB::table('tbl_parent')->where('parent_id', $id)->first();

        if (!$parent) {
            return redirect()->back()->with('error', 'Parent not found.');
        }

        DB::table('tbl_parent')->where('parent_id', $id)->delete();

        return redirect()->back()->with('success', 'Parent deleted successfully.');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Error deleting parent: ' . $e->getMessage());
    }
}
 // Store a new violation
    public function storeViolation(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:tbl_student,student_id',
            'offense_sanc_id' => 'required|exists:tbl_offenses_with_sanction,offense_sanc_id',
            'violation_incident' => 'required|string',
            'violation_date' => 'required|date',
            'violation_time' => 'required',
        ]);

        $adviserId = Auth::guard('adviser')->id();

        $violation = ViolationRecord::create([
            'student_id' => $request->student_id,
            'adviser_id' => $adviserId,
            'prefect_id' => null,
            'offense_sanc_id' => $request->offense_sanc_id,
            'violation_incident' => $request->violation_incident,
            'violation_date' => $request->violation_date,
            'violation_time' => $request->violation_time,
        ]);

        return response()->json(['success' => true, 'violation' => $violation]);
    }

    // Update a violation
    public function updateViolation(Request $request, $id)
    {
        $violation = ViolationRecord::findOrFail($id);

        $request->validate([
            'student_id' => 'required|exists:tbl_student,student_id',
            'offense_sanc_id' => 'required|exists:tbl_offenses_with_sanction,offense_sanc_id',
            'violation_incident' => 'required|string',
            'violation_date' => 'required|date',
            'violation_time' => 'required',
        ]);

        $violation->update([
            'student_id' => $request->student_id,
            'offense_sanc_id' => $request->offense_sanc_id,
            'violation_incident' => $request->violation_incident,
            'violation_date' => $request->violation_date,
            'violation_time' => $request->violation_time,
        ]);

        return response()->json(['success' => true, 'violation' => $violation]);
    }

    // Delete a violation
    public function destroyViolation($id)
    {
        $violation = ViolationRecord::findOrFail($id);
        $violation->delete();

        return response()->json(['success' => true]);
    }


public function storeComplaintsAppointment(Request $request)
{
    $request->validate([
        'complaint_id' => 'required|exists:complaints,id',
        'comp_app__date' => 'required|date',
        'comp_app__time' => 'required',
        'comp_app__status' => 'required|string',
    ]);

    ComplaintsAppointment::create([
        'complaint_id' => $request->complaint_id,
        'comp_app_date' => $request->appointment_date,
        'comp_app__time' => $request->appointment_time,
        'comp_app__status' => $request->appointment_status,
    ]);

    return redirect()->back()->with('success', 'Appointment created successfully!');
}

public function storeViolationAppointment(Request $request)
{
    $request->validate([
        'violation_id' => 'required|exists:tbl_violation_record,violation_id',
        'violation_app_date' => 'required|date',
        'violation_app_time' => 'required',
        'violation_app_status' => 'required|string',
    ]);

    ViolationAppointment::create([
        'violation_id' => $request->violation_id,
        'violation_app_date' => $request->violation_app_date,
        'violation_app_time' => $request->violation_app_time,
        'violation_app_status' => $request->violation_app_status,
    ]);

    return redirect()->route('violation.appointment')->with('success', 'Appointment created successfully.');
}

}
