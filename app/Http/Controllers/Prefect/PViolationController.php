<?php

namespace App\Http\Controllers\Prefect;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Auth; // Assuming Prefect is authenticated

use App\Models\ViolationRecord;

class PViolationController extends Controller
{



public function store(Request $request)
    {
        // Validate inputs
        $request->validate([
            'student_id' => 'required|array',
            'student_id.*' => 'required|exists:tbl_student,student_id',
            'offense' => 'required|array',
            'offense.*' => 'required|exists:tbl_offenses_with_sanction,offense_sanc_id',
            'date' => 'required|array',
            'date.*' => 'required|date',
            'time' => 'required|array',
            'time.*' => 'required',
            'incident' => 'required|array',
            'incident.*' => 'required|string',
        ]);

        $studentIds = $request->input('student_id');
        $offenseIds = $request->input('offense');
        $dates      = $request->input('date');
        $times      = $request->input('time');
        $incidents  = $request->input('incident');

        // Get Prefect ID from Auth (assuming Prefect is logged in)
        $prefectId = Auth::guard('prefect')->id();

        // Loop through all records and save
        foreach ($studentIds as $index => $studentId) {
            $violation = new ViolationRecord();
            $violation->student_id = $studentId;
            $violation->offense_sanc_id = $offenseIds[$index];
            $violation->prefect_id = $prefectId;
            $violation->violation_date = $dates[$index];
            $violation->violation_time = $times[$index];
            $violation->violation_incident = $incidents[$index];
            $violation->adviser_id = null; // Assign if available
            $violation->save();
        }

        return redirect()->back()->with('messages', ['Violations saved successfully!']);
    }
}
