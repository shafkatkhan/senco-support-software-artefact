@extends('layouts.auth', ['title' => 'Login'])

@section('content')
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
        <span id="login_error_message"></span>
        <button type="button" class="close" onclick="$(this).parent().hide()" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div> 
</div>
@endsection

@push('scripts')
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
            error: function(xhr) {
                var message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'There was an error logging in.';
                var message2 = xhr.responseJSON && xhr.responseJSON.message2 ? xhr.responseJSON.message2 : 'Please try again.';

                $('#login_error_message').html(message + ' <strong style="font-weight:500;">' + message2 + '</strong>');

                $('.alert').show().addClass('show');
                setTimeout(function(){ 
                    $('.alert').hide().removeClass('show');
                }, 3000);
            }
        });
    });
});
</script>
@endpush