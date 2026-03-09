<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ is_rtl() ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/fontawesome.css') }}" type="text/css" />
    @if(is_rtl())
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.rtl.min.css" integrity="sha384-CfCrinSRH2IR6a4e6fy2q6ioOX7O6Mtm1L9vRvFZ1trBncWmMePhzvafv7oIcWiW" crossorigin="anonymous">
    @else
        <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}" type="text/css" />
    @endif
    <link type="text/css" rel="stylesheet" href="{{ asset('css/login.css') }}">
    <title>MySencoSupportSoftware | MFA Challenge</title>
</head>
<body class="login_body">
    <section id="loginBox">
        <div class="login_logo">
            <img src="{{ asset('img/logo.png') }}" alt="MySencoSupportSoftware">
        </div>
        <div class="login_form">
            <div class="mfa_title">
                {{ __('Two-Factor Authentication') }}
            </div>
            <div class="mfa_caption">
                {{ __('Please enter your 6-digit PIN') }}
            </div>
            <form method="POST" action="{{ route('mfa-challenge.verify') }}">
                @csrf
                <div class="form-group mb-3">
                    <input id="pin" type="text" class="form-control mfa_pin" placeholder="123456" name="pin" required maxlength="6" pattern="\d{6}" autofocus autocomplete="one-time-code">
                </div>
                
                @error('pin')
                    <div class="alert alert-danger mt-3">{{ $message }}</div>
                @enderror
                @if(session('error'))
                    <div class="alert alert-danger mt-3">{{ session('error') }}</div>
                @endif
                
                <button id="login_btn" class="btn btn-lg btn-block mt-4 w-100" type="submit" style="color: #fff">{{ __('Verify') }}</button>
            </form>
            
            <div class="text-center mt-3">
                 <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-link back_to_login"><i class="fas fa-arrow-left"></i> {{ __('Back to Login') }}</button>
                </form>
            </div>
        </div>
        <div class="login_info">
            <div>
               © MySencoSupportSoftware {{ date("Y") }} 
            </div>
            <div>
                <a href="https://shafkatkhan.com">
                    Shafkat Khan | KCL 2026
                </a>                
            </div>
        </div>
    </section>
</body>
</html>
