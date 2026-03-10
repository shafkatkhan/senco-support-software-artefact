<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailMfaCode;
use App\Models\Setting;

class MfaChallengeController extends Controller
{
    public function index()
    {
        // if challenge not needed, redirect away
        if (!auth()->user()->isMfaPending()) {
            return redirect('/');
        }
        
        $mfa_method = Setting::get('mfa_method', 'none');
        $user = auth()->user();

        // if setup required rather than challenge
        if (!$user->mfa_verified_at) {
            return redirect()->route('mfa-setup.index');
        }

        if ($mfa_method == 'email') {
            $rateLimitKey = 'mfa_email_sent_' . $user->id;
            
            if (!Cache::has($rateLimitKey)) {
                $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                Cache::put('mfa_challenge_code_' . $user->id, $code, now()->addMinutes(15));
                Cache::put($rateLimitKey, true, now()->addMinute());
                
                try {
                    Mail::to($user->email)->send(new EmailMfaCode($code));
                } catch (\Exception $e) {
                    return back()->with('error', __('Failed to send MFA email. Please contact your administrator.'));
                }
            }
        }

        return view('mfa_challenge', compact('mfa_method'));
    }

    public function verify(Request $request)
    {
        $request->validate([
            'pin' => 'required|string|size:6',
        ]);

        $user = auth()->user();
        $mfa_method = Setting::get('mfa_method', 'none');
        $valid = false;

        if ($mfa_method == 'authenticator_app') {
            $google2fa = new Google2FA();
            $valid = $google2fa->verifyKey($user->mfa_secret, $request->pin);
        } elseif ($mfa_method == 'email') {
            $cachedCode = Cache::get('mfa_challenge_code_' . $user->id);
            if ($cachedCode && $cachedCode == $request->pin) {
                $valid = true;
                Cache::forget('mfa_challenge_code_' . $user->id);
            }
        }

        if ($valid) {
            $request->session()->put('mfa_session_verified', true);
            return redirect()->intended('/');
        }

        return back()->with('error', __('Invalid code. Please try again.'));
    }
}
