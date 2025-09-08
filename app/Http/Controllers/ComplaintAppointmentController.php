<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ComplaintsAppointment;

class ComplaintAppointmentController extends Controller

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
public function updateComplaintAppointment(Request $request, $id)
{
   
    $validated = $request->validate([
        'complaint_id'     => 'required|exists:complaints,id',
        'comp_app_date'    => 'required|date',
        'comp_app_time'    => 'required',
        'comp_app_status'  => 'required|string',
    ]);

    $appointment = ComplaintsAppointment::findOrFail($id);


    $appointment->update($validated);

    return redirect()->route('complaint.appointment')
                     ->with('success', 'Complaint appointment updated successfully.');
}
}
