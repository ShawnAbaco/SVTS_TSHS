<?php

namespace App\Http\Controllers\Prefect;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Adviser;

class PDashboardController extends Controller
{


    public function dashboard()
    {
        $advisers = Adviser::all(); // Fetch all advisers for the dashboard
        return view('prefect.dashboard', compact('advisers'));
    }

}
