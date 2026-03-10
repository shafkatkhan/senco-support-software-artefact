<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailMfaCode;

class MfaSetupController extends Controller
{
    public function index()
    {
        $title = 'MFA Setup';
        $mfa_method = Setting::get('mfa_method', 'none');
        $user = auth()->user();
        
        $qrCodeSvg = null;
        $mfaSecret = null;

        if ($mfa_method == 'authenticator_app' && !$user->mfa_verified_at) {
            $google2fa = new Google2FA();

            // generate a secret key
            if (!$user->mfa_secret) {
                $user->mfa_secret = $google2fa->generateSecretKey();
                $user->save();
            }

            $mfaSecret = $user->mfa_secret;

            $qrCodeUrl = $google2fa->getQRCodeUrl(
                config('app.name'),
                $user->username,
                $mfaSecret
            );
            
            $renderer = new ImageRenderer(
                new RendererStyle(200),
                new SvgImageBackEnd()
            );
            $writer = new Writer($renderer);
            $qrCodeSvg = $writer->writeString($qrCodeUrl);
        } elseif ($mfa_method == 'email' && !$user->mfa_verified_at) {
            // check if recently sent an email to avoid spam/rate limits
            $rateLimitKey = 'mfa_email_sent_' . $user->id;
            
            if (!Cache::has($rateLimitKey)) {
                // generate a random 6-digit code
                $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                
                // cache the code for 15 minutes
                Cache::put('mfa_setup_code_' . $user->id, $code, now()->addMinutes(15));
                
                // set a 1-minute rate limit on sending emails
                Cache::put($rateLimitKey, true, now()->addMinute());
                
                // send the email
                try {
                    Mail::to($user->email)->send(new EmailMfaCode($code));
                } catch (\Exception $e) {
                    return back()->with('error', __('Failed to send MFA email. Please check the system SMTP settings.'));
                }
            }
        }

        return view('mfa_setup', compact('title', 'mfa_method', 'qrCodeSvg', 'mfaSecret'));
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
            $cachedCode = Cache::get('mfa_setup_code_' . $user->id);
            if ($cachedCode && $cachedCode == $request->pin) {
                $valid = true;
                Cache::forget('mfa_setup_code_' . $user->id); // clear the code once used
            }
        }

        if ($valid) {
            $user->mfa_verified_at = now();
            $user->save();
            $request->session()->put('mfa_session_verified', true);
            return redirect()->route('mfa-setup.index')->with('success', __('MFA has been successfully verified and activated.'));
        }

        return redirect()->route('mfa-setup.index')->with('error', __('Invalid code. Please try again.'));
    }

    public function reset(Request $request)
    {
        $user = auth()->user();
        $user->mfa_secret = null;
        $user->mfa_verified_at = null;
        $user->save();
        $request->session()->forget('mfa_session_verified');

        return redirect()->route('mfa-setup.index')->with('success', __('MFA settings have been reset, please configure them again.'));
    }
}
