<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

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

        $user = User::create([
            'display_name' => $request->input('display_name'),
            'email' => $request->email,
            'password_hash' => Hash::make($request->password),
            'is_email_verified' => false,
            'subscription_status' => 'Free',
        ]);

        return response()->json([
            'user' => $user,
            'token' => $user->createToken('api_token')->plainTextToken,
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
}
