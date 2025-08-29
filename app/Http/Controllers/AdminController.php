<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\Adviser;
use App\Models\OffensesWithSanction;
use App\Models\ParentModel;

class AdminController extends Controller
{
    public function showLoginForm()
    {
        return view('prefect.login');
    }

    public function login(Request $request)
{
    $credentials = $request->only('email', 'password');

    // Map 'email' input to 'prefect_email' in DB
    $credentials = [
        'prefect_email' => $credentials['email'],
        'password'      => $credentials['password'],
    ];

    if (Auth::guard('prefect')->attempt($credentials)) {
    return redirect('/prefect/dashboard');
    }

    return back()->withErrors(['email' => 'Invalid credentials.']);
}

    public function dashboard()
    {
        $advisers = Adviser::all(); // Fetch all advisers for the dashboard
        return view('prefect.dashboard', compact('advisers'));
    }

    public function studentmanagement()
    {
        return view('prefect.studentmanagement');
    }
     public function peoplecomplaints()
    {
        return view('prefect.peoplecomplaints');
    }

     public function parentlists()
    {
        $parents = ParentModel::with(['students.adviser'])->get();
    return view('prefect.parentlists', compact("parents"));
    }
    public function violationrecords()
    {
        return view('prefect.violationrecords');
    }

    public function violationappointments()
    {
        return view('prefect.violationappointments');
    }

    public function violationanecdotals()
    {
        return view('prefect.violationanecdotals');
    }
public function reportgenerate()
    {
        return view('prefect.reportgenerate');
    }
   public function usermanagement()
{
    $advisers = Adviser::all(); // Fetch all advisers
    return view('prefect.usermanagement', compact('advisers'));
}


    public function complaintsappointments()
    {
        return view('prefect.complaintsappointments');
    }
    
     public function complaintsanecdotals()
    {
        return view('prefect.complaintsanecdotals');
    }
     public function offensesandsanctions()
    {
    $offenses = OffensesWithSanction::orderBy('offense_sanc_id', 'ASC')->get();
    return view('prefect.offensesandsanctions', compact('offenses'));
    }

    public function createAdviser(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:advisers',
            'password' => 'required|min:6',
        ]);

        Adviser::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Adviser created successfully.');
    }
    
}
