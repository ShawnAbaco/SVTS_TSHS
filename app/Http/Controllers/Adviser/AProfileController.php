<?php

namespace App\Http\Controllers\Adviser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Models\Adviser;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AProfileController extends Controller
{
    public function sendVerificationCode(Request $request)
    {
        try {
            $user = Auth::guard('adviser')->user();
            
            Log::info('Adviser send verification code attempt', ['user_id' => $user ? $user->adviser_id : 'none']);
            
            if (!$user) {
                Log::error('Adviser not authenticated in adviser guard');
                return response()->json([
                    'message' => 'User not authenticated. Please log in again.'
                ], 401);
            }

            // Generate 6-digit verification code
            $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            
            // Store code in cache for 10 minutes
            $cacheKey = 'adviser_password_verification_code_' . $user->adviser_id;
            Cache::put($cacheKey, $verificationCode, 600); // 10 minutes
            
            Log::info('Adviser verification code generated', [
                'user_id' => $user->adviser_id,
                'email' => $user->adviser_email,
                'cache_key' => $cacheKey
            ]);

            // Send email with verification code
            $data = [
                'name' => $user->adviser_fname . ' ' . $user->adviser_lname,
                'email' => $user->adviser_email,
                'verification_code' => $verificationCode,
                'valid_minutes' => 10
            ];

            Log::info('Attempting to send email to adviser', ['email' => $user->adviser_email]);

            Mail::send('emails.adviser-password-verification', $data, function($message) use ($user) {
                $message->to($user->adviser_email)
                        ->subject('Password Change Verification Code - Student Violation Tracking System');
            });

            Log::info('Adviser verification code sent successfully', ['email' => $user->adviser_email]);

            return response()->json([
                'message' => 'Verification code sent to your email successfully!'
            ]);

        } catch (\Exception $e) {
            Log::error('Adviser verification email failed: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => isset($user) ? $user->adviser_id : 'unknown'
            ]);
            
            return response()->json([
                'message' => 'Failed to send verification code: ' . $e->getMessage()
            ], 500);
        }
    }

    public function changePassword(Request $request)
    {
        try {
            $user = Auth::guard('adviser')->user();
            
            if (!$user) {
                return response()->json([
                    'message' => 'User not authenticated'
                ], 401);
            }

            $validator = Validator::make($request->all(), [
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

            // Verify the verification code
            $cachedCode = Cache::get('adviser_password_verification_code_' . $user->adviser_id);
            
            if (!$cachedCode || $cachedCode !== $request->verification_code) {
                return response()->json([
                    'message' => 'Invalid or expired verification code.'
                ], 422);
            }

            // Update password directly (no current password check)
            $user->adviser_password = Hash::make($request->new_password);
            $user->save();

            // Clear the verification code
            Cache::forget('adviser_password_verification_code_' . $user->adviser_id);

            // Send confirmation email
            $this->sendPasswordChangeConfirmation($user);

            return response()->json([
                'message' => 'Password changed successfully!'
            ]);

        } catch (\Exception $e) {
            Log::error('Adviser password change failed: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'An error occurred while changing password.'
            ], 500);
        }
    }

    private function sendPasswordChangeConfirmation($user)
    {
        try {
            $data = [
                'name' => $user->adviser_fname . ' ' . $user->adviser_lname,
                'email' => $user->adviser_email,
                'change_date' => now()->format('F j, Y \a\t g:i A'),
                'ip_address' => request()->ip()
            ];

            Mail::send('emails.adviser-password-changed', $data, function($message) use ($user) {
                $message->to($user->adviser_email)
                        ->subject('Password Changed Successfully - Student Violation Tracking System');
            });

            Log::info('Adviser password change confirmation sent', ['email' => $user->adviser_email]);

        } catch (\Exception $e) {
            Log::error('Adviser password change confirmation email failed: ' . $e->getMessage());
        }
    }

    public function getProfileInfo(Request $request)
{
    try {
        Log::info('Adviser profile info requested');
        
        $user = Auth::guard('adviser')->user();
        
        if (!$user) {
            Log::warning('Adviser not authenticated in profile info request');
            return response()->json([
                'message' => 'User not authenticated'
            ], 401);
        }

        Log::info('Adviser profile data', [
            'adviser_id' => $user->adviser_id,
            'name' => $user->adviser_fname . ' ' . $user->adviser_lname,
            'email' => $user->adviser_email
        ]);

        $responseData = [
            'name' => $user->adviser_fname . ' ' . $user->adviser_lname,
            'email' => $user->adviser_email,
            'gender' => $user->adviser_sex,
            'contact' => $user->adviser_contactinfo,
            'gradelevel' => $user->adviser_gradelevel,
            'section' => $user->adviser_section,
            'status' => $user->status,
            'profile_image' => $user->profile_image ? Storage::url($user->profile_image) : '/images/user.jpg'
        ];

        Log::info('Sending profile response', $responseData);

        return response()->json($responseData);

    } catch (\Exception $e) {
        Log::error('Get adviser profile info failed: ' . $e->getMessage());
        
        return response()->json([
            'message' => 'Failed to load profile information'
        ], 500);
    }
}

    public function uploadProfileImage(Request $request)
    {
        try {
            $user = Auth::guard('adviser')->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $validator = Validator::make($request->all(), [
                'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            // Delete old profile image if exists
            if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
                Storage::disk('public')->delete($user->profile_image);
            }

            // Store new image
            $imagePath = $request->file('profile_image')->store('adviser_profile_images', 'public');
            
            // Update user record
            $user->profile_image = $imagePath;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Profile image uploaded successfully',
                'image_url' => Storage::url($imagePath)
            ]);

        } catch (\Exception $e) {
            Log::error('Adviser profile image upload failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload profile image: ' . $e->getMessage()
            ], 500);
        }
    }

    public function removeProfileImage(Request $request)
    {
        try {
            $user = Auth::guard('adviser')->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Delete image file if exists
            if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
                Storage::disk('public')->delete($user->profile_image);
            }

            // Update user record
            $user->profile_image = null;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Profile image removed successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Adviser profile image removal failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove profile image'
            ], 500);
        }
    }
}