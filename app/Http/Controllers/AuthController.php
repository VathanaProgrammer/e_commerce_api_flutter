<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
         Session::start();
         
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        try {
            Log::info('Login attempt', [
                'username' => $request->username,
                'is_active' => 1
            ]);

            $credentials = [
                'username' => $request->username,
                'password' => $request->password,
            ];
            

            if (Auth::attempt($credentials, $request->filled('remember'))) {
                $request->session()->regenerate();

                Log::info('Login successful', ['username' => $request->username]);

                return redirect()->intended('/');
            }

            Log::warning('Login failed: invalid credentials', ['username' => $request->username]);

            return back()->withErrors([
                'username' => 'Invalid credentials',
            ])->withInput();

        } catch (\Throwable $e) {
            Log::error('Login exception', [
                'username' => $request->username,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors([
                'username' => 'Something went wrong. Please try again later.',
            ])->withInput();
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Log::info('User logged out', ['username' => $request->user()?->username]);

        return redirect('/login');
    }
}