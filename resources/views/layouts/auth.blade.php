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
    <title>{{ $title ?? 'MySencoSupportSoftware' }}</title>
</head>
<body class="login_body">
    <section id="loginBox">
        <div class="login_logo">
            <img src="{{ asset('img/logo.png') }}" alt="MySencoSupportSoftware">
        </div>
        
        @yield('content')
        
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
@stack('scripts')
</body>
</html>