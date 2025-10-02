<?php

namespace App\Http\Controllers\Adviser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth; // Assuming Prefect is authenticated
use App\Models\OffensesWithSanction;
use App\Models\Complaints;

class AComplaintController extends Controller
{
public function complaintsall()
{
    // $adviserId = Auth::guard('adviser')->id();

    // $complaints = Complaints::with(['complainant','respondent','offense'])
    //     ->whereHas('complainant', fn($q) => $q->where('adviser_id', $adviserId))
    //     ->orWhereHas('respondent', fn($q) => $q->where('adviser_id', $adviserId))
    //     ->get();

    // // Add these lines:
    // $offenses = OffensesWithSanction::all();

    // return view('adviser.complaintsall', compact('complaints', 'offenses'));
     $complaints = Complaints::with([
        'complainant',
        'respondent',
        'offense'
    ])->get();

    return view('adviser.complaintsall', compact('complaints'));

}

}