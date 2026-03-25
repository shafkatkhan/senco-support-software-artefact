<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Setting;
use App\Models\User;

class MfaSettingController extends Controller
{
    public function index()
    {
        Gate::authorize('manage-mfa-settings');

        $title = __('MFA Settings');
        $mfa_method = Setting::get('mfa_method', 'none');
        $smtp_configured = Setting::get('mail_host');
        return view('mfa_settings', compact('title', 'mfa_method', 'smtp_configured'));
    }

    public function update(Request $request)
    {
        Gate::authorize('manage-mfa-settings');

        $request->validate([
            'mfa_method' => 'required|in:none,email,authenticator_app',
        ]);

        if ($request->mfa_method == 'email') {
            if (!Setting::get('mail_host')) {
                return redirect()->back()->with('error', __('You must configure your Email Settings before enabling Email MFA.'));
            }
        }

        $old_mfa_method = Setting::get('mfa_method', 'none');
        
        Setting::set('mfa_method', $request->mfa_method);

        if ($old_mfa_method !== $request->mfa_method) {
            User::query()->update([
                'mfa_secret' => null,
                'mfa_verified_at' => null,
            ]);
        }

        return redirect()->route('mfa-settings.index')->with('success', __(':type settings updated successfully!', ['type' => 'MFA']));
    }
}
