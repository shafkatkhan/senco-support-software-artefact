@extends('layouts.app')

@section('content')
    <section id="content">
        <div class="settings_wrap">
            <form action="{{ route('mfa-settings.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="settings_section">
                    <div class="title">{{ __('Multi-Factor Authentication') }}</div>
                    <div class="description">{{ __('Choose how users must verify their identity when logging in.') }}</div>

                    <div class="mfa_options">
                        <label class="mfa_option {{ $mfa_method === 'none' ? 'active' : '' }}">
                            <input type="radio" name="mfa_method" value="none" {{ $mfa_method === 'none' ? 'checked' : '' }}>
                            <div class="mfa_option_content">
                                <div class="mfa_option_icon"><i class="fas fa-lock-open"></i></div>
                                <div class="text">
                                    <div class="mfa_option_title">{{ __('No MFA') }}</div>
                                    <div class="mfa_option_description">{{ __('Users log in with username and password only. No additional verification step.') }}</div>
                                </div>
                            </div>
                        </label>
                        <label class="mfa_option {{ $mfa_method === 'email' ? 'active' : '' }}">
                            <input type="radio" name="mfa_method" value="email" {{ $mfa_method === 'email' ? 'checked' : '' }}>
                            <div class="mfa_option_content">
                                <div class="mfa_option_icon"><i class="fas fa-envelope"></i></div>
                                <div class="text">
                                    <div class="mfa_option_title">{{ __('Email Verification') }}</div>
                                    <div class="mfa_option_description">{{ __('A one-time code is sent to the user\'s email address after login. Users must enter the code to continue.') }}</div>
                                </div>
                            </div>
                        </label>
                        <label class="mfa_option {{ $mfa_method === 'authenticator_app' ? 'active' : '' }}">
                            <input type="radio" name="mfa_method" value="authenticator_app" {{ $mfa_method === 'authenticator_app' ? 'checked' : '' }}>
                            <div class="mfa_option_content">
                                <div class="mfa_option_icon"><i class="fas fa-mobile-alt"></i></div>
                                <div class="text">
                                    <div class="mfa_option_title">{{ __('Authenticator App') }}</div>
                                    <div class="mfa_option_description">{{ __('Users must set up an Authenticator app and enter a time-based code each time they log in.') }}</div>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="settings_actions">
                    <button type="submit" class="btn btn-success">{{ __('Save Changes') }}</button>
                </div>
            </form>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('.mfa_option input[type="radio"]').on('change', function () {
                $('.mfa_option').removeClass('active');
                $(this).closest('.mfa_option').addClass('active');
            });
        });
    </script>
@endpush