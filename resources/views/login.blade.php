<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/fontawesome.css') }}" type="text/css" />
    @if(app()->getLocale() == 'ar')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.rtl.min.css" integrity="sha384-CfCrinSRH2IR6a4e6fy2q6ioOX7O6Mtm1L9vRvFZ1trBncWmMePhzvafv7oIcWiW" crossorigin="anonymous">
    @else
        <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}" type="text/css" />
    @endif
    <link type="text/css" rel="stylesheet" href="{{ asset('css/login.css') }}">
    <title>MySencoSupportSoftware | Login</title>
</head>
<body class="login_body">
    <section id="loginBox">
        <div class="login_logo">
            <img src="{{ asset('img/logo.png') }}" alt="MySencoSupportSoftware">
        </div>
        <div class="login_form">
            <div class="text">
                <span>{{ __('Welcome,') }}</span> {{ __('please login') }}
            </div>
            <form id="loginForm">
                @csrf
                <div class="form-group">
                    <input id="username" type="text" class="form-control" placeholder="{{ __('Username') }}" name="username" required>
                </div>
                <div class="form-group">
                    <input id="password" type="password" class="form-control" placeholder="{{ __('Password') }}" name="password" required>
                </div>
                <button id="login_btn" class="btn btn-lg btn-primary btn-block" type="submit">{{ __('Login') }}</button>
            </form>
            <div class="alert alert-danger alert-dismissible fade" role="alert" style="display:none;">
                {{ __('Username or password is incorrect.') }} <strong style="font-weight:500;">{{ __('Please try again.') }}</strong>
                <button type="button" class="close" onclick="$(this).parent().hide()">
                    <span aria-hidden="true">&times;</span>
                </button>
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script>
$(document).ready(function(){
    $('#loginForm').submit(function(e){
        e.preventDefault();
        var username = $('#username').val();
        var password = $('#password').val();
        var _token = $('input[name="_token"]').val();

        $.ajax({
            url: '{{ url('/login') }}',
            type: 'POST',
            data: {
                username: username,
                password: password,
                _token: _token
            },
            success: function(response) {
                if(response == 'success'){
                    window.location.href = "{{ url('/') }}";
                }
            },
            error: function() {
                $('.alert').show().addClass('show');
                setTimeout(function(){ 
                    $('.alert').hide().removeClass('show');
                }, 3000);
            }
        });
    });
});
</script>
</body>
</html>