<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ViolationRecord;

abstract class ViolationRecordController
{
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

    $violation = ViolationRecord::create([
        'violator_id' => $request->student_id,
        'prefect_id' => 1, 
        'offense_sanc_id' => $request->offense_sanc_id,
        'violation_incident' => $request->violation_incident,
        'violation_date' => $request->violation_date,
        'violation_time' => $request->violation_time,
    ]);

    // 🔥 Reload violation with relationships so frontend gets complete data
    $violation = ViolationRecord::with([
        'student.parent', 
        'offense'
    ])->find($violation->violation_id);

    return response()->json([
        'success' => true,
        'violation' => $violation
    ]);
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

}
