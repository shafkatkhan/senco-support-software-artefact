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

                    @if(!auth()->user()->mfa_verified_at)
                        <hr>
                        <div class="mfa_setup_action mt-4">
                            <div class="left">
                                <div class="title">{{ __('Check your email inbox') }}</div>
                                <div class="description">
                                    {{ __('We have sent a 6-digit verification code to your email address. It will expire in 15 minutes.') }}
                                </div>
                                <div class="text-center mt-4">
                                    <i class="fas fa-envelope-open-text text-primary" style="font-size: 3rem; opacity: 0.8;"></i>
                                </div>
                                <div class="text-center mt-4">
                                    <form method="GET" action="{{ route('mfa-setup.index') }}">
                                        <button type="submit" class="btn btn-outline-secondary btn-sm">{{ __('Resend Code') }}</button>
                                    </form>
                                </div>
                            </div>

                            <div class="right">
                                <form method="POST" action="{{ route('mfa-setup.verify') }}" class="mt-4">
                                    @csrf
                                    <div class="form-group mb-3">
                                        <label for="pin" class="form-label text-start d-block">{{ __('Enter 6-digit code:') }}</label>
                                        <input type="text" name="pin" id="pin" class="form-control text-center fs-4 letter-spacing-2" placeholder="123456" required maxlength="6" pattern="\d{6}">
                                        @error('pin')
                                            <div class="text-danger mt-1 text-start">{{ $message }}</div>
                                        @enderror
                                        @if(session('error'))
                                            <div class="text-danger mt-1 text-start">{{ session('error') }}</div>
                                        @endif
                                    </div>
                                    <button type="submit" class="btn btn-success w-100">{{ __('Verify & Activate') }}</button>
                                </form>
                            </div>
                        </div>
                    @endif
                @elseif($mfa_method === 'authenticator_app')
                    <div class="mfa_setup_status mfa_status_pending">
                        <div class="mfa_setup_status_icon"><i class="fas fa-mobile-alt"></i></div>
                        <div class="mfa_setup_status_text">
                            <div class="mfa_setup_status_title">{{ __('Authenticator App') }}</div>
                            <div class="mfa_setup_status_description">{{ __('Your administrator requires authenticator app verification. You will need to scan the QR code with your authenticator app.') }}</div>
                        </div>
                    </div>
                    
                    @if(!auth()->user()->mfa_verified_at)
                        <hr>
                        <div class="mfa_setup_action mt-4">
                            <div class="left">
                                <div class="title">{{ __('Scan the following QR Code') }}</div>
                                <div class="description">
                                    {{ __('Open your authenticator app (e.g., Google Authenticator, Authy) and scan the QR code below. Alternatively, you can manually enter the secret key.') }}
                                </div>
                                
                                <div class="text-center mb-4">
                                    {!! $qrCodeSvg !!}
                                </div>
                                
                                <div class="text-center mb-4">
                                    <strong>{{ __('Secret Key:') }}</strong> <code style="font-size: 1.2rem; letter-spacing: 1px;">{{ $mfaSecret }}</code>
                                </div>
                            </div>

                            <div class="right">
                                <form method="POST" action="{{ route('mfa-setup.verify') }}" class="mt-4">
                                    @csrf
                                    <div class="form-group mb-3">
                                        <label for="pin" class="form-label text-start d-block">{{ __('Enter 6-digit code:') }}</label>
                                        <input type="text" name="pin" id="pin" class="form-control text-center fs-4 letter-spacing-2" placeholder="123456" required maxlength="6" pattern="\d{6}">
                                        @error('pin')
                                            <div class="text-danger mt-1 text-start">{{ $message }}</div>
                                        @enderror
                                        @if(session('error'))
                                            <div class="text-danger mt-1 text-start">{{ session('error') }}</div>
                                        @endif
                                    </div>
                                    <button type="submit" class="btn btn-success w-100">{{ __('Verify & Activate') }}</button>
                                </form>
                            </div>
                        </div>
                    @endif
                @endif

                @if($mfa_method !== 'none' && auth()->user()->mfa_verified_at)
                    <div class="mfa_setup_verified d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-check-circle"></i>
                            {{ __('MFA is set up and active on your account.') }}
                            <span class="text-muted d-block mt-1" style="font-size: 0.85em;">{{ __('Verified') }}: {{ auth()->user()->mfa_verified_at->format('d M Y') }}</span>
                        </div>
                        <form method="POST" action="{{ route('mfa-setup.reset') }}" onsubmit="return confirm('{{ __('Are you sure you want to reset your MFA settings? You will be required to set it up again.') }}');">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-sm">{{ __('Reset MFA') }}</button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection