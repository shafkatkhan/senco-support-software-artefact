<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;

class MfaChallengeController extends Controller
{
    public function index()
    {
        // if challenge not needed, redirect away
        if (!auth()->user()->isMfaPending()) {
            return redirect('/');
        }
        
        // if setup required rather than challenge
        if (!auth()->user()->mfa_verified_at) {
            return redirect()->route('mfa-setup.index');
        }

        return view('mfa_challenge');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'pin' => 'required|string|size:6',
        ]);

        $user = auth()->user();
        $google2fa = new Google2FA();

        $valid = $google2fa->verifyKey($user->mfa_secret, $request->pin);

        if ($valid) {
            $request->session()->put('mfa_session_verified', true);
            return redirect()->intended('/');
        }

        return back()->with('error', __('Invalid PIN. Please try again.'));
    }
}
