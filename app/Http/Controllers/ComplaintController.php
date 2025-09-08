<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Complaints;

class ComplaintController extends Controller
{
    public function storeComplaint(Request $request)
{
    $validated = $request->validate([
        'complainant_id'       => 'required|exists:tbl_student,student_id',
        'respondent_id'        => 'required|exists:tbl_student,student_id',
        'prefect_id'           => 'required|exists:tbl_prefect_of_discipline,prefect_id',
        'offense_sanc_id'      => 'required|exists:tbl_offenses_with_sanction,offense_sanc_id',
        'complaints_incident'  => 'required|string',
        'complaints_date'      => 'required|date',
        'complaints_time'      => 'required',
    ]);

    Complaints::create($validated);

    return redirect()->route('complaints.index')
                     ->with('success', 'Complaint created successfully.');
}
public function updateComplaint(Request $request, $id)
{
    // Validate input
    $validated = $request->validate([
        'complainant_id'       => 'required|exists:tbl_student,student_id',
        'respondent_id'        => 'required|exists:tbl_student,student_id',
        'prefect_id'           => 'required|exists:tbl_prefect_of_discipline,prefect_id',
        'offense_sanc_id'      => 'required|exists:tbl_offenses_with_sanction,offense_sanc_id',
        'complaints_incident'  => 'required|string',
        'complaints_date'      => 'required|date',
        'complaints_time'      => 'required',
    ]);

    $complaint = Complaints::findOrFail($id);

    $complaint->update($validated);

    return redirect()->route('complaints.index')
                     ->with('success', 'Complaint updated successfully.');
}

}
