<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class MfaSetupController extends Controller
{
    public function index()
    {
        $title = 'MFA Setup';
        $mfa_method = Setting::get('mfa_method', 'none');
        return view('mfa_setup', compact('title', 'mfa_method'));
    }
}
