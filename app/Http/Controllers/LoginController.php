<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if ($user->expiry_date?->isPast()) {
                Auth::logout();

                return response()->json([
                    'message' => __('This user account has expired.'),
                    'message2' => __('Please contact your administrator.'),
                ], 403);
            }

            $request->session()->regenerate();

            return response()->json('success');
        }

        return response()->json([
            'message' => __('Username or password is incorrect.'),
            'message2' => __('Please try again.'),
        ], 401);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
