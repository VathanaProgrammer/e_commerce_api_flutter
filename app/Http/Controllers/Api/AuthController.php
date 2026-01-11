<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    // Login with Sanctum token
    public function login(Request $request)
    {
        // Validate request
        $request->validate([
            'username' => 'required|string',
            'password' => 'required',
        ]);

        // Find user by email (or username)
        $user = User::where('username', $request->username)->first();

        // Check credentials
        if (!$user || !Hash::check($request->password, $user->password_hash)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        // Revoke previous tokens if needed
        $user->tokens()->delete();

        // Create a new Sanctum token
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'prefix' => $user->prefix,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'gender' => $user->gender,
                'profile_image_url' => $user->profile_image_url,
                'is_active' => $user->is_active ? true : false,
                'last_login' => $user->last_login,
                'username' => $user->username,
                'role' => $user->role,
            ],
            'token' => $token,
        ]);
    }

    // Optional: Logout endpoint
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    // Optional: Authenticated user info
    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'user' => $request->user()
        ]);
    }
}