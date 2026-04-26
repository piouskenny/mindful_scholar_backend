<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user.
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'school_id' => 'nullable|exists:schools,id',
            'level' => 'nullable|string|max:50',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'school_id' => $validated['school_id'] ?? null,
            'level' => $validated['level'] ?? null,
        ]);

        $otpCode = rand(100000, 999999);
        \App\Models\Otp::updateOrCreate(
            ['email' => $validated['email']],
            ['otp' => $otpCode, 'expires_at' => now()->addMinutes(10)]
        );
        \Illuminate\Support\Facades\Mail::to($validated['email'])->send(new \App\Mail\OtpMail($otpCode));

        return response()->json([
            'message' => 'Account created successfully. Please check your email for the OTP.',
            'email' => $user->email,
        ], 201);
    }

    /**
     * Login user and return token.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        if (is_null($user->email_verified_at)) {
            return response()->json([
                'message' => 'Please verify your email address before logging in.',
                'email_not_verified' => true,
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'school_id' => $user->school_id,
                'level' => $user->level,
                'cgpa' => $user->cgpa,
                'profile_picture' => $user->profile_picture,
            ],
            'token' => $token,
        ]);
    }

    /**
     * Logout user (revoke token).
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    /**
     * Verify OTP and login user.
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string',
        ]);

        $otpRecord = \App\Models\Otp::where('email', $request->email)->first();

        if (!$otpRecord || $otpRecord->otp !== $request->otp) {
            throw ValidationException::withMessages([
                'otp' => ['The provided OTP is incorrect.'],
            ]);
        }

        if (now()->greaterThan($otpRecord->expires_at)) {
            throw ValidationException::withMessages([
                'otp' => ['The provided OTP has expired.'],
            ]);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $user->email_verified_at = now();
        $user->save();

        $otpRecord->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Email verified successfully. You are now logged in.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'school_id' => $user->school_id,
                'level' => $user->level,
                'cgpa' => $user->cgpa,
                'profile_picture' => $user->profile_picture,
            ],
            'token' => $token,
        ]);
    }

    /**
     * Resend OTP.
     */
    public function resendOtp(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->firstOrFail();

        if ($user->email_verified_at) {
            return response()->json([
                'message' => 'Email is already verified.',
            ], 400);
        }

        $otpCode = rand(100000, 999999);
        \App\Models\Otp::updateOrCreate(
            ['email' => $request->email],
            ['otp' => $otpCode, 'expires_at' => now()->addMinutes(10)]
        );
        \Illuminate\Support\Facades\Mail::to($request->email)->send(new \App\Mail\OtpMail($otpCode));

        return response()->json([
            'message' => 'A new OTP has been sent to your email.',
        ]);
    }

    /**
     * Get authenticated user profile.
     */
    public function user(Request $request): JsonResponse
    {
        $user = $request->user();
        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'school_id' => $user->school_id,
                'level' => $user->level,
                'cgpa' => $user->cgpa,
                'profile_picture' => $user->profile_picture,
            ],
        ]);
    }

    /**
     * Get list of schools.
     */
    public function schools(): JsonResponse
    {
        return response()->json([
            'schools' => \App\Models\School::all(['id', 'name', 'short_name']),
        ]);
    }

    /**
     * Update user profile.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'username' => 'sometimes|string|max:50|unique:users,username,' . $user->id,
            'level' => 'sometimes|string|max:50',
            'cgpa' => 'sometimes|numeric|between:0,5.00',
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'school_id' => $user->school_id,
                'level' => $user->level,
                'cgpa' => $user->cgpa,
                'profile_picture' => $user->profile_picture,
            ],
        ]);
    }

    /**
     * Upload profile picture.
     */
    public function uploadProfilePicture(Request $request): JsonResponse
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $user = $request->user();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('profile_pictures', 'public');
            
            // Optionally delete old picture
            // if ($user->profile_picture) {
            //     Storage::disk('public')->delete($user->profile_picture);
            // }

            $user->profile_picture = $path;
            $user->save();

            return response()->json([
                'message' => 'Profile picture uploaded successfully',
                'profile_picture' => $path,
                'url' => asset('storage/' . $path),
            ]);
        }

        return response()->json(['message' => 'No image uploaded'], 400);
    }
}
