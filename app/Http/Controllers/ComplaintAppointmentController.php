<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ComplaintsAppointment;

abstract class ComplaintAppointmentController
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
}
