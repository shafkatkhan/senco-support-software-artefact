@extends('layouts.app')

@section('content')
    <section id="content">
        <div class="settings_wrap">
            <div class="settings_section">
                <div class="title">{{ __('MFA Setup') }}</div>
                <div class="description">{{ __('Set up multi-factor authentication for your account.') }}</div>

                @if($mfa_method === 'none')
                    <div class="mfa_setup_status mfa_status_disabled">
                        <div class="mfa_setup_status_icon"><i class="fas fa-lock-open"></i></div>
                        <div class="mfa_setup_status_text">
                            <div class="mfa_setup_status_title">{{ __('MFA is not required') }}</div>
                            <div class="mfa_setup_status_description">{{ __('Your administrator has not enabled multi-factor authentication. No action is needed.') }}</div>
                        </div>
                    </div>
                @elseif($mfa_method === 'email')
                    <div class="mfa_setup_status mfa_status_pending">
                        <div class="mfa_setup_status_icon"><i class="fas fa-envelope"></i></div>
                        <div class="mfa_setup_status_text">
                            <div class="mfa_setup_status_title">{{ __('Email Verification') }}</div>
                            <div class="mfa_setup_status_description">{{ __('Your administrator requires email-based verification. A one-time code will be sent to your email each time you log in.') }}</div>
                        </div>
                    </div>
                @elseif($mfa_method === 'authenticator_app')
                    <div class="mfa_setup_status mfa_status_pending">
                        <div class="mfa_setup_status_icon"><i class="fas fa-mobile-alt"></i></div>
                        <div class="mfa_setup_status_text">
                            <div class="mfa_setup_status_title">{{ __('Authenticator App') }}</div>
                            <div class="mfa_setup_status_description">{{ __('Your administrator requires authenticator app verification. You will need to scan the QR code with your authenticator app.') }}</div>
                        </div>
                    </div>
                @endif

                @if(auth()->user()->mfa_verified_at)
                    <div class="mfa_setup_verified">
                        <i class="fas fa-check-circle"></i>
                        {{ __('MFA is set up and active on your account.') }}
                        <span class="text-muted">{{ __('Verified') }}: {{ auth()->user()->mfa_verified_at->format('d M Y') }}</span>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection