@extends('layouts.app')

@section('content')
    <section id="content">        
        <div class="row settings_wrap email_settings_wrap">
            <div class="col-md-8 d-flex flex-column">
                <div class="settings_section">
                    <div class="title">
                        <i class="fas fa-envelope me-2"></i>{{ __('SMTP Configuration') }}
                    </div>
                    <div class="description">
                        {{ __('Configure your mail server settings to allow the application to send outgoing emails (like MFA codes or notifications).') }}
                    </div>
                    <form action="{{ route('email-settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label class="form-label">{{ __('SMTP Host') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="mail_host" value="{{ $settings['mail_host'] }}" required placeholder="{{ __('e.g. smtp.mailgun.org') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ __('SMTP Port') }} <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="mail_port" value="{{ $settings['mail_port'] }}" required placeholder="587">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Username') }}</label>
                                <input type="text" class="form-control" name="mail_username" value="{{ $settings['mail_username'] }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Password') }}</label>
                                <input type="text" class="form-control" name="mail_password" value="{{ $settings['mail_password'] }}">
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Encryption') }}</label>
                                <select class="form-select" name="mail_encryption">
                                    <option value="" {{ empty($settings['mail_encryption']) ? 'selected' : '' }}>{{ __('None') }}</option>
                                    <option value="tls" {{ $settings['mail_encryption'] == 'tls' ? 'selected' : '' }}>TLS</option>
                                    <option value="ssl" {{ $settings['mail_encryption'] == 'ssl' ? 'selected' : '' }}>SSL</option>
                                </select>
                            </div>
                        </div>
                        <hr>
                        <div class="title" style="margin-bottom: 25px;">
                            <i class="fas fa-paper-plane me-2"></i>{{ __('Sender Identity') }}
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('From Address') }} <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="mail_from_address" value="{{ $settings['mail_from_address'] }}" required placeholder="noreply@mysencosupport.com">
                                <div class="form-text text-muted">{{ __('The email address your emails will be sent from.') }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('From Name') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="mail_from_name" value="{{ $settings['mail_from_name'] }}" required placeholder="MySencoSupportSoftware">
                                <div class="form-text text-muted">{{ __('The name displayed as the sender.') }}</div>
                            </div>
                        </div>
                        <div class="settings_actions">
                            <button type="submit" class="btn btn-success">
                                {{ __('Save Settings') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-4 d-flex flex-column">
                <div class="settings_section" style="background-color: #ffffff91;">
                    <div class="title">
                        <i class="fas fa-info-circle me-2"></i>{{ __('Instructions') }}
                    </div>
                    <div class="description">
                        {{ __('To send emails from the system, you need an SMTP server. This could be provided by your web host, Microsoft 365, Google Workspace, or a dedicated transactional email service like Mailgun or SendGrid.') }}
                    </div>
                    <ul class="small text-muted list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>Host & Port:</strong> {{ __('Usually provided by your email service provider.') }}
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>Username:</strong> {{ __('Often your email address, or an API key username.') }}
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>Password:</strong> {{ __('Your email password, an App Password, or an API secret.') }}
                        </li>
                        <li>
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>Encryption:</strong> {{ __('TLS is recommended for port 587, SSL for port 465.') }}
                        </li>
                    </ul>
                </div>
                <div class="settings_section" style="margin-top: 25px;">
                    <div class="title">
                        <i class="fas fa-paper-plane me-2"></i>{{ __('Send Test Email') }}
                    </div>
                    <div class="description">{{ __('Make sure to save your settings before sending a test email.') }}</div>
                    <form action="{{ route('email-settings.test') }}" method="POST">
                        @csrf
                        <label class="form-label">{{ __('Destination Email') }}</label>
                        <input type="email" class="form-control" name="test_email_address" required placeholder="john.smith@example.com" value="{{ Auth::user()->email }}">
                        <button type="submit" class="btn btn-outline-primary mt-3">
                            {{ __('Send Test') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection