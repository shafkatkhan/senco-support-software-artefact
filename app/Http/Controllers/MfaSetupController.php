<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Writer;

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
        }

        return view('mfa_setup', compact('title', 'mfa_method', 'qrCodeSvg', 'mfaSecret'));
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
            $user->mfa_verified_at = now();
            $user->save();
            return redirect()->route('mfa-setup.index')->with('success', __('MFA has been successfully verified and activated.'));
        }

        return redirect()->route('mfa-setup.index')->with('error', __('Invalid PIN. Please try again.'));
    }

    public function reset(Request $request)
    {
        $user = auth()->user();
        $user->mfa_secret = null;
        $user->mfa_verified_at = null;
        $user->save();

        return redirect()->route('mfa-setup.index')->with('success', __('MFA settings have been reset, please configure them again.'));
    }
}
