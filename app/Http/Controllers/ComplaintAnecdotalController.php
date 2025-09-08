<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use app\Models\ComplaintsAnecdotal;

abstract class ComplaintAnecdotalController
{
public function storeComplaintAnecdotal(Request $request)
{

    $validated = $request->validate([
        'complaints_id'            => 'required|exists:tbl_complaints,complaints_id',
        'comp_anec_solution'       => 'required|string',
        'comp_anec_recommendation' => 'required|string',
        'comp_anec_date'           => 'required|date',
        'comp_anec_time'           => 'required',
    ]);


    ComplaintsAnecdotal::create($validated);

    return redirect()->route('complaints.anecdotal.index')
                     ->with('success', 'Complaint anecdotal created successfully.');
}
public function updateComplaintAnecdotal(Request $request, $id)
{
    $validated = $request->validate([
        'complaints_id'            => 'required|exists:tbl_complaints,complaints_id',
        'comp_anec_solution'       => 'required|string',
        'comp_anec_recommendation' => 'required|string',
        'comp_anec_date'           => 'required|date',
        'comp_anec_time'           => 'required',
    ]);

    $anecdotal = ComplaintsAnecdotal::findOrFail($id);

    $anecdotal->update($validated);

    return redirect()->route('complaints.anecdotal.index')
                     ->with('success', 'Complaint anecdotal updated successfully.');
}

}
