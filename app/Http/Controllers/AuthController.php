<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PrefectOfDiscipline;
use App\Models\Adviser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    
    public function showLoginForm()
    {
        return view('login');
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

    public function sendPasswordResetCode(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email'
            ]);

            $user = PrefectOfDiscipline::where('prefect_email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'message' => 'No account found with this email address.'
                ], 404);
            }

            // Generate 6-digit verification code
            $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            
            // Store code in cache for 10 minutes
            $cacheKey = 'password_reset_code_' . $user->prefect_id;
            Cache::put($cacheKey, $verificationCode, 600); // 10 minutes

            // Send email with verification code
            $data = [
                'name' => $user->prefect_fname . ' ' . $user->prefect_lname,
                'email' => $user->prefect_email,
                'verification_code' => $verificationCode,
                'valid_minutes' => 10
            ];

            Mail::send('emails.password-reset', $data, function($message) use ($user) {
                $message->to($user->prefect_email)
                        ->subject('Password Reset Verification Code - Student Violation Tracking System');
            });

            return response()->json([
                'message' => 'Verification code sent to your email successfully!'
            ]);

        } catch (\Exception $e) {
            \Log::error('Password reset email failed: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Failed to send verification code. Please try again.'
            ], 500);
        }
    }

    public function verifyResetCode(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'verification_code' => 'required|string|size:6'
            ]);

            $user = PrefectOfDiscipline::where('prefect_email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'message' => 'No account found with this email address.'
                ], 404);
            }

            // Verify the verification code
            $cachedCode = Cache::get('password_reset_code_' . $user->prefect_id);
            
            if (!$cachedCode) {
                return response()->json([
                    'message' => 'Verification code has expired. Please request a new one.'
                ], 422);
            }

            if ($cachedCode !== $request->verification_code) {
                return response()->json([
                    'message' => 'Invalid verification code. Please try again.'
                ], 422);
            }

            return response()->json([
                'message' => 'Verification code is valid.'
            ]);

        } catch (\Exception $e) {
            \Log::error('Verification code check failed: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'An error occurred while verifying the code.'
            ], 500);
        }
    }

    public function resetPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'verification_code' => 'required|string|size:6',
                'new_password' => 'required|min:6|confirmed',
            ], [
                'new_password.confirmed' => 'New password confirmation does not match.',
                'verification_code.size' => 'Verification code must be 6 digits.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = PrefectOfDiscipline::where('prefect_email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'message' => 'No account found with this email address.'
                ], 404);
            }

            // Verify the verification code
            $cachedCode = Cache::get('password_reset_code_' . $user->prefect_id);
            
            if (!$cachedCode || $cachedCode !== $request->verification_code) {
                return response()->json([
                    'message' => 'Invalid or expired verification code.'
                ], 422);
            }

            // Update password
            $user->prefect_password = Hash::make($request->new_password);
            $user->save();

            // Clear the verification code
            Cache::forget('password_reset_code_' . $user->prefect_id);

            // Send confirmation email
            $this->sendPasswordResetConfirmation($user);

            return response()->json([
                'message' => 'Password reset successfully! You can now login with your new password.'
            ]);

        } catch (\Exception $e) {
            \Log::error('Password reset failed: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'An error occurred while resetting password.'
            ], 500);
        }
    }

    private function sendPasswordResetConfirmation($user)
    {
        try {
            $data = [
                'name' => $user->prefect_fname . ' ' . $user->prefect_lname,
                'email' => $user->prefect_email,
                'reset_date' => now()->format('F j, Y \a\t g:i A'),
                'ip_address' => request()->ip()
            ];

            Mail::send('emails.password-reset-confirmation', $data, function($message) use ($user) {
                $message->to($user->prefect_email)
                        ->subject('Password Reset Successfully - Student Violation Tracking System');
            });

        } catch (\Exception $e) {
            \Log::error('Password reset confirmation email failed: ' . $e->getMessage());
        }
    }
}