<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ViolationAnecdotal;

abstract class ViolationAnecdotalController
{
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
