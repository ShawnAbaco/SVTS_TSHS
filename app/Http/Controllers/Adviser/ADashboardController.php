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
use App\Models\ViolationRecord;

class ADashboardController extends Controller
{

     public function dashboard()
    {
        $adviserId = Auth::guard('adviser')->id();

        $studentsCount   = Student::where('adviser_id', $adviserId)->count();
        $violationsCount = ViolationRecord::whereHas('student', fn($q) => $q->where('adviser_id', $adviserId))->count();
        $complaintsCount = Complaints::whereHas('complainant', fn($q) => $q->where('adviser_id', $adviserId))
                                    ->orWhereHas('respondent', fn($q) => $q->where('adviser_id', $adviserId))
                                    ->count();

        return view('adviser.dashboard', compact('studentsCount','violationsCount','complaintsCount'));
    }
    
}