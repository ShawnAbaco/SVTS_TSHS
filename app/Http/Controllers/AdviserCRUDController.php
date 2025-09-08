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
use App\Models\Adviser;

class AdviserCRUDController extends Controller
{
public function storeAdviser(Request $request)
{
    $validated = $request->validate([
        'adviser_fname'       => 'required|string|max:255',
        'adviser_lname'       => 'required|string|max:255',
        'adviser_email'       => 'required|email|max:255|unique:tbl_adviser,adviser_email',
        'adviser_password'    => 'required|string|min:8',
        'adviser_contactinfo' => 'required|string|max:255',
        'adviser_section'     => 'required|string|max:255',
        'adviser_gradelevel'  => 'required|string|max:50',
    ]);

    $validated['adviser_password'] = bcrypt($validated['adviser_password']);

    Adviser::create($validated);

    return redirect()->route('advisers.index')
                     ->with('success', 'Adviser created successfully.');
}
public function updateAdviser(Request $request, $id)
{
    $validated = $request->validate([
        'adviser_fname'       => 'required|string|max:255',
        'adviser_lname'       => 'required|string|max:255',
        'adviser_email'       => 'required|email|max:255|unique:tbl_adviser,adviser_email,' . $id . ',adviser_id',
        'adviser_password'    => 'nullable|string|min:8',
        'adviser_contactinfo' => 'required|string|max:255',
        'adviser_section'     => 'required|string|max:255',
        'adviser_gradelevel'  => 'required|string|max:50',
    ]);

    $adviser = Adviser::findOrFail($id);
    if (!empty($validated['adviser_password'])) {
        $validated['adviser_password'] = bcrypt($validated['adviser_password']);
    } else {
        unset($validated['adviser_password']);
    }

    $adviser->update($validated);

    return redirect()->route('advisers.index')
                     ->with('success', 'Adviser updated successfully.');
}


}
