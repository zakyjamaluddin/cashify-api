<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendOtpMail;

class AuthController extends Controller
{
    // Register
    public function register(Request $request)
    {
        $request->validate([
            'display_name' => 'nullable|string|max:100',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $otp = rand(100000, 999999);

        $user = User::create([
            'display_name' => $request->input('display_name'),
            'email' => $request->email,
            'password_hash' => Hash::make($request->password),
            'is_email_verified' => false,
            'subscription_status' => 'Free',
            'otp_code' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(10),
        ]);

        Mail::to($user->email)->send(new SendOtpMail($otp));

        return response()->json([
            'message' => 'OTP telah dikirim ke email Anda. Silahkan melakukan verifikasi OTP.',
            'user_id' => $user->id,
        ], 201);


        
    }

    // Login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password_hash)) {
            return response()->json(['message' => 'Email atau password salah.'], 401);
        }

        if (! $user->is_verified) {
            return response()->json(['message' => 'Akun belum diverifikasi.'], 403);
        }

        return response()->json([
            'user' => $user,
            'token' => $user->createToken('api_token')->plainTextToken,
        ]);
    }

    // Logout
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logout berhasil',
        ]);
    }

    // Verify OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'user_id' => 'required|uuid',
            'otp' => 'required|numeric',
        ]);

        $user = User::find($request->user_id);

        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan.'], 404);
        }

        if ($user->is_verified) {
            return response()->json(['message' => 'Akun sudah diverifikasi.']);
        }

        if ($user->otp_code != $request->otp) {
            return response()->json(['message' => 'Kode OTP salah.'], 400);
        }

        if (Carbon::now()->gt($user->otp_expires_at)) {
            return response()->json(['message' => 'Kode OTP telah kedaluwarsa.'], 400);
        }

        $user->update([
            'is_verified' => true,
            'otp_code' => null,
            'otp_expires_at' => null,
        ]);

        return response()->json(['message' => 'Email berhasil diverifikasi.']);
    }

    // Resend OTP
    public function resendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan.'], 404);
        }

        if ($user->is_verified) {
            return response()->json(['message' => 'Akun sudah diverifikasi.']);
        }

        $otp = rand(100000, 999999);
        $user->update([
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        Mail::to($user->email)->send(new SendOtpMail($otp));

        return response()->json(['message' => 'OTP baru telah dikirim ke email.']);
    }

}
