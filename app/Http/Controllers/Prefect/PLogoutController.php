<?php

namespace App\Http\Controllers\Prefect;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Adviser;
use Illuminate\Support\Facades\Auth;


class PLogoutController extends Controller
{

        public function logout(Request $request)
{
    Auth::guard('prefect')->logout();

    // Optionally invalidate the session
    $request->session()->invalidate();
    $request->session()->regenerateToken();

     return response()->json([
                'success' => true,
                'message' => 'Logout successful!',
                'redirect' => route('adviser.login')
            ]);
}

}