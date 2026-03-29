@extends('layouts.app')

@section('content')
    <section id="content">        
        <div class="settings_wrap email_settings_wrap">
            <div class="settings_section">
                <div class="title">
                    <i class="fas fa-user-circle me-2"></i>{{ __('Profile Settings') }}
                </div>
                <div class="description">
                    {{ __('Update your account details and change your password.') }}
                </div>
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('First Name') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="first_name" value="{{ old('first_name', $user->first_name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Last Name') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="last_name" value="{{ old('last_name', $user->last_name) }}" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Username') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="username" value="{{ old('username', $user->username) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Email Address') }} <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" value="{{ old('email', $user->email) }}" required>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Mobile') }}</label>
                            <input type="text" class="form-control" name="mobile" value="{{ old('mobile', $user->mobile) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Position') }}</label>
                            <input type="text" class="form-control" name="position" value="{{ old('position', $user->position) }}">
                        </div>
                    </div>
                    <hr>
                    <div class="title">
                        <i class="fas fa-lock me-2"></i>{{ __('Change Password') }}
                    </div>
                    <div class="description">
                        {{ __('Leave blank if you do not wish to change your password.') }}
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('New Password') }}</label>
                            <input type="password" class="form-control" name="password">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Confirm Password') }}</label>
                            <input type="password" class="form-control" name="password_confirmation">
                        </div>
                    </div>
                    <div class="settings_actions">
                        <button type="submit" class="btn btn-success">
                            {{ __('Save Changes') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
