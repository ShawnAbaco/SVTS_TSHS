<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ViolationAppointment;

class ViolationAppointmentController extends Controller
{
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
public function updateViolationAppointment(Request $request, $id)
{
    // Validate request
    $validated = $request->validate([
        'violation_id' => 'required|exists:tbl_violation_record,violation_id',
        'violation_app_date' => 'required|date',
        'violation_app_time' => 'required',
        'violation_app_status' => 'required|string',
    ]);

    // Find the appointment or fail
    $appointment = ViolationAppointment::findOrFail($id);

    // Update with validated data
    $appointment->update($validated);

    return redirect()->route('violation.appointment')
                     ->with('success', 'Appointment updated successfully.');
}

}
