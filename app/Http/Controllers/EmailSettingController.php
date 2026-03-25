<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Gate;
use Exception;
use App\Models\Setting;
use App\Mail\TestEmail;

class EmailSettingController extends Controller
{
    public function index()
    {
        Gate::authorize('manage-email-settings');

        $title = __('Email Settings');
        
        $settings = [
            'mail_host' => Setting::get('mail_host'),
            'mail_port' => Setting::get('mail_port'),
            'mail_username' => Setting::get('mail_username'),
            'mail_password' => Setting::get('mail_password'),
            'mail_encryption' => Setting::get('mail_encryption'),
            'mail_from_address' => Setting::get('mail_from_address'),
            'mail_from_name' => Setting::get('mail_from_name'),
        ];

        return view('email_settings', compact('title', 'settings'));
    }

    public function update(Request $request)
    {
        Gate::authorize('manage-email-settings');

        $request->validate([
            'mail_host' => 'required|string',
            'mail_port' => 'required|numeric',
            'mail_username' => 'nullable|string',
            'mail_password' => 'nullable|string',
            'mail_encryption' => 'nullable|string',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string',
        ]);

        Setting::set('mail_host', $request->mail_host);
        Setting::set('mail_port', $request->mail_port);
        Setting::set('mail_username', $request->mail_username);
        Setting::set('mail_password', $request->mail_password);
        Setting::set('mail_encryption', $request->mail_encryption);
        Setting::set('mail_from_address', $request->mail_from_address);
        Setting::set('mail_from_name', $request->mail_from_name);

        return redirect()->back()->with('success', __(':type settings updated successfully!', ['type' => __('Email')]));
    }

    public function test(Request $request)
    {
        Gate::authorize('manage-email-settings');

        $request->validate([
            'test_email_address' => 'required|email'
        ]);

        try {
            Mail::to($request->test_email_address)->send(new TestEmail());
            return redirect()->back()->with('success', __('Test email sent successfully to :email!', ['email' => $request->test_email_address]));
        } catch (Exception $e) {
            return redirect()->back()->with('error', __('Failed to send test email: :error.', ['error' => $e->getMessage()]));
        }
    }
}