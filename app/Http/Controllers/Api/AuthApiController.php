<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AuthApiController extends Controller
{
    // ── Login ──────────────────────────────────────────────
    public function login(Request $request)
    {
        $request->validate([
            'seller_id' => 'required',
            'password'  => 'required',
        ]);

        $user = User::where('seller_id', $request->seller_id)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid seller ID or password',
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status'  => true,
            'message' => 'Login successful',
            'token'   => $token,
            'user'    => $user,
        ]);
    }

    // ── Logout ─────────────────────────────────────────────
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Logged out successfully',
        ]);
    }

    // ── Me (authenticated user) ────────────────────────────
    public function me(Request $request)
    {
        return response()->json([
            'status' => true,
            'data'   => $request->user(),
        ]);
    }

    // ── Forgot Password (send OTP) ─────────────────────────
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'seller_id' => 'required',
        ]);

        $user = User::where('seller_id', $request->seller_id)->first();

        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'No account found with this seller ID',
            ], 404);
        }

        // Generate 6-digit OTP
        $otp = rand(100000, 999999);

        // Store OTP in cache for 10 minutes (key = seller_id)
        Cache::put('otp_' . $request->seller_id, $otp, now()->addMinutes(10));

        // TODO: Send OTP via SMS / email / notification
        // Mail::to($user->email)->send(new OtpMail($otp));
        // You can also log it temporarily during development:
        \Log::info('OTP for ' . $request->seller_id . ': ' . $otp);

        return response()->json([
            'status'  => true,
            'message' => 'OTP sent successfully',
            // Remove 'otp' from response in production
            'otp'     => $otp,
        ]);
    }

    // ── Verify OTP ─────────────────────────────────────────
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'seller_id' => 'required',
            'otp'       => 'required',
        ]);

        $cachedOtp = Cache::get('otp_' . $request->seller_id);

        if (!$cachedOtp) {
            return response()->json([
                'status'  => false,
                'message' => 'OTP has expired. Please request a new one',
            ], 400);
        }

        if ((string) $cachedOtp !== (string) $request->otp) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid OTP',
            ], 400);
        }

        // OTP verified — generate a temporary reset token
        $resetToken = Str::random(64);

        // Store reset token in cache for 15 minutes
        Cache::put('reset_token_' . $request->seller_id, $resetToken, now()->addMinutes(15));

        // Clear the OTP
        Cache::forget('otp_' . $request->seller_id);

        return response()->json([
            'status'  => true,
            'message' => 'OTP verified successfully',
            'token'   => $resetToken,
        ]);
    }

    // ── Reset Password ─────────────────────────────────────
    public function resetPassword(Request $request)
    {
        $request->validate([
            'seller_id'             => 'required',
            'token'                 => 'required',
            'password'              => 'required|min:6|confirmed',
            // frontend must also send 'password_confirmation'
        ]);

        $cachedToken = Cache::get('reset_token_' . $request->seller_id);

        if (!$cachedToken || $cachedToken !== $request->token) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid or expired reset token',
            ], 400);
        }

        $user = User::where('seller_id', $request->seller_id)->first();

        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'User not found',
            ], 404);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Clear reset token
        Cache::forget('reset_token_' . $request->seller_id);

        // Revoke all existing tokens for security
        $user->tokens()->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Password reset successfully. Please log in again.',
        ]);
    }
}