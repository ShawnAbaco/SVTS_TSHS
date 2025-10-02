<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PrefectOfDiscipline;
use App\Models\Adviser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    
    public function showLoginForm()
    {
        return view('adviser.login');
    }

    public function login(Request $request)
{
    $request->validate([
        'email'    => 'required|email',
        'password' => 'required',
    ]);

    // Try Prefect
    if (Auth::guard('prefect')->attempt(['prefect_email' => $request->email, 'password' => $request->password])) {
        return response()->json([
            'success'  => true,
            'message'  => 'Login successful',
            'redirect' => route('prefect.dashboard'),
        ]);
    }

    // Try Adviser
    if (Auth::guard('adviser')->attempt(['adviser_email' => $request->email, 'password' => $request->password])) {
        return response()->json([
            'success'  => true,
            'message'  => 'Login successful',
            'redirect' => route('adviser.dashboard'),
        ]);
    }

    return response()->json([
        'success' => false,
        'message' => 'Invalid credentials provided.',
    ]);
}

}
