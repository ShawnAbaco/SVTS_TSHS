<?php

namespace App\Http\Controllers\Prefect;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Models\PrefectOfDiscipline;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function sendVerificationCode(Request $request)
    {
        try {
            $user = Auth::guard('prefect')->user();

            Log::info('Send verification code attempt', ['user_id' => $user ? $user->prefect_id : 'none']);

            if (!$user) {
                Log::error('User not authenticated in prefect guard');
                return response()->json([
                    'message' => 'User not authenticated. Please log in again.'
                ], 401);
            }

            // Generate 6-digit verification code
            $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            // Store code in cache for 10 minutes
            $cacheKey = 'password_verification_code_' . $user->prefect_id;
            Cache::put($cacheKey, $verificationCode, 600); // 10 minutes

            Log::info('Verification code generated', [
                'user_id' => $user->prefect_id,
                'email' => $user->prefect_email,
                'cache_key' => $cacheKey
            ]);

            // Send email with verification code
            $data = [
                'name' => $user->prefect_fname . ' ' . $user->prefect_lname,
                'email' => $user->prefect_email,
                'verification_code' => $verificationCode,
                'valid_minutes' => 10
            ];

            Log::info('Attempting to send email', ['email' => $user->prefect_email]);

            Mail::send('emails.password-verification', $data, function($message) use ($user) {
                $message->to($user->prefect_email)
                        ->subject('Password Change Verification Code - Student Violation Tracking System');
            });

            Log::info('Verification code sent successfully', ['email' => $user->prefect_email]);

            return response()->json([
                'message' => 'Verification code sent to your email successfully!'
            ]);

        } catch (\Exception $e) {
            Log::error('Verification email failed: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => isset($user) ? $user->prefect_id : 'unknown'
            ]);

            return response()->json([
                'message' => 'Failed to send verification code: ' . $e->getMessage()
            ], 500);
        }
    }

    public function changePassword(Request $request)
    {
        try {
            $user = Auth::guard('prefect')->user();

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
            $cachedCode = Cache::get('password_verification_code_' . $user->prefect_id);

            if (!$cachedCode || $cachedCode !== $request->verification_code) {
                return response()->json([
                    'message' => 'Invalid or expired verification code.'
                ], 422);
            }

            // Update password directly (no current password check)
            $user->prefect_password = Hash::make($request->new_password);
            $user->save();

            // Clear the verification code
            Cache::forget('password_verification_code_' . $user->prefect_id);

            // Send confirmation email
            $this->sendPasswordChangeConfirmation($user);

            return response()->json([
                'message' => 'Password changed successfully!'
            ]);

        } catch (\Exception $e) {
            Log::error('Password change failed: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while changing password.'
            ], 500);
        }
    }

    private function sendPasswordChangeConfirmation($user)
    {
        try {
            $data = [
                'name' => $user->prefect_fname . ' ' . $user->prefect_lname,
                'email' => $user->prefect_email,
                'change_date' => now()->format('F j, Y \a\t g:i A'),
                'ip_address' => request()->ip()
            ];

            Mail::send('emails.password-changed', $data, function($message) use ($user) {
                $message->to($user->prefect_email)
                        ->subject('Password Changed Successfully - Student Violation Tracking System');
            });

            Log::info('Password change confirmation sent', ['email' => $user->prefect_email]);

        } catch (\Exception $e) {
            Log::error('Password change confirmation email failed: ' . $e->getMessage());
        }
    }
public function getProfileInfo(Request $request)
{
    try {
        $user = Auth::guard('prefect')->user();

        if (!$user) {
            return response()->json([
                'message' => 'User not authenticated'
            ], 401);
        }

        // UPDATED: Check if profile_image already contains full path or just relative path
        if ($user->profile_image) {
            // If it's already a full URL (shouldn't be, but just in case)
            if (filter_var($user->profile_image, FILTER_VALIDATE_URL)) {
                $profileImageUrl = $user->profile_image;
            }
            // If it starts with storage/ (old format), convert it
            else if (str_starts_with($user->profile_image, 'storage/')) {
                $profileImageUrl = asset($user->profile_image);
            }
            // If it's a relative path in public/ folder
            else if (file_exists(public_path($user->profile_image))) {
                $profileImageUrl = asset($user->profile_image);
            }
            // Fallback to default
            else {
                $profileImageUrl = asset('images/user.jpg');
            }
        } else {
            $profileImageUrl = asset('images/user.jpg');
        }

        return response()->json([
            'name' => $user->prefect_fname . ' ' . $user->prefect_lname,
            'email' => $user->prefect_email,
            'gender' => $user->prefect_sex,
            'contact' => $user->prefect_contactinfo,
            'status' => $user->status,
            'profile_image' => $profileImageUrl
        ]);

    } catch (\Exception $e) {
        Log::error('Get profile info failed: ' . $e->getMessage());

        return response()->json([
            'message' => 'Failed to load profile information'
            // Optional: add for debugging
            // 'debug' => $e->getMessage()
        ], 500);
    }
}
public function uploadProfileImage(Request $request)
{
    try {
        $user = Auth::guard('prefect')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        // Delete old profile image if exists and not default
        if ($user->profile_image) {
            $oldImagePath = public_path($user->profile_image);
            // Check if file exists and is not the default image
            if (file_exists($oldImagePath) && !str_contains($user->profile_image, 'user.jpg')) {
                unlink($oldImagePath);
            }
        }

        // Store new image in public directory for Hostinger
        $image = $request->file('profile_image');
        $imageName = 'profile_' . $user->id . '_' . time() . '.' . $image->getClientOriginalExtension();

        // Create directory if it doesn't exist
        $directory = public_path('images/profiles');
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        // Move image to public directory
        $image->move($directory, $imageName);

        // Save relative path from public folder
        $imageRelativePath = 'images/profiles/' . $imageName;

        // Update user record with relative path
        $user->profile_image = $imageRelativePath;
        $user->save();

        // Return full URL using asset() helper
        $imageUrl = asset($imageRelativePath);

        return response()->json([
            'success' => true,
            'message' => 'Profile image uploaded successfully',
            'image_url' => $imageUrl,
            'profile_image' => $imageRelativePath
        ]);

    } catch (\Exception $e) {
        Log::error('Profile image upload failed: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Failed to upload profile image: ' . $e->getMessage()
        ], 500);
    }
}

   public function removeProfileImage(Request $request)
{
    try {
        $user = Auth::guard('prefect')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        // Delete image file if exists and it's not the default image
        if ($user->profile_image) {
            $imagePath = public_path($user->profile_image);

            // Check if file exists and is not the default user.jpg
            if (file_exists($imagePath) && !str_contains($user->profile_image, 'user.jpg')) {
                unlink($imagePath);
            }
        }

        // Set to default image path (not null)
        $user->profile_image = 'images/user.jpg';
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile image removed successfully',
            'image_url' => asset('images/user.jpg') // Return the default image URL
        ]);

    } catch (\Exception $e) {
        Log::error('Profile image removal failed: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Failed to remove profile image: ' . $e->getMessage()
        ], 500);
    }
}
}
