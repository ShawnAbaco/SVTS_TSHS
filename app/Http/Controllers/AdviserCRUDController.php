<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ViolationRecord;
use App\Models\ViolationAppointment;
use App\Models\ComplaintsAppointment;
use App\Models\ViolationAnecdotal;

class AdviserCRUDController extends Controller
{

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
 public function anecdotalStore(Request $request)
    {
        $request->validate([
            'violation_id' => 'required|exists:tbl_violation_record,violation_id',
            'violation_anec_solution' => 'required|string|max:255',
            'violation_anec_recommendation' => 'required|string|max:255',
            'violation_anec_date' => 'required|date',
            'violation_anec_time' => 'required',
        ]);

        ViolationAnecdotal::create([
            'violation_id' => $request->violation_id,
            'violation_anec_solution' => $request->violation_anec_solution,
            'violation_anec_recommendation' => $request->violation_anec_recommendation,
            'violation_anec_date' => $request->violation_anec_date,
            'violation_anec_time' => $request->violation_anec_time,
        ]);

        return redirect()->back()->with('success', 'Anecdotal record created successfully.');
    }

    // Optional: Update
    public function anecdotalUpdate(Request $request, $id)
    {
        $record = ViolationAnecdotal::findOrFail($id);
        $record->update($request->all());
        return redirect()->back()->with('success', 'Record updated.');
    }

    // Optional: Delete
    public function anecdotalDelete($id)
    {
        $record = ViolationAnecdotal::findOrFail($id);
        $record->delete();
        return response()->json(['success' => true]);
    }
}
