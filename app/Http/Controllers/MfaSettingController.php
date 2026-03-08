<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class MfaSettingController extends Controller
{
    public function index()
    {
        $title = 'MFA Settings';
        $mfa_method = Setting::get('mfa_method', 'none');
        return view('mfa_settings', compact('title', 'mfa_method'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'mfa_method' => 'required|in:none,email,google_authenticator',
        ]);
        Setting::set('mfa_method', $request->mfa_method);

        return redirect()->route('mfa-settings.index')->with('success', 'MFA settings updated successfully!');
    }
}
