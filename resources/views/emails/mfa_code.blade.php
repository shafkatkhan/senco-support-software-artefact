<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ __('MFA Verification Code') }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 5px; text-align: center; margin-bottom: 20px;">
        <h2 style="color: #0c3674; margin-top: 0;">{{ __('MFA Verification') }}</h2>
        <p style="font-size: 16px; margin-bottom: 0;">{{ __('Here is your one-time verification code.') }}</p>
    </div>
    <div style="padding: 20px; border: 1px solid #dee2e6; border-radius: 5px; text-align: center;">
        <p>{{ __('Please enter the following 6-digit code to continue.') }}</p>
        <div style="background-color: #e9ecef; margin: 20px 0; padding: 15px; border-radius: 4px; display: inline-block;">
            <code style="font-size: 32px; font-weight: bold; letter-spacing: 5px; color: #0c3674;">{{ $code }}</code>
        </div>
        <p style="font-size: 14px; color: #6c757d;">
            {{ __('This code will expire in 15 minutes.') }}<br>
            {{ __('If you did not request this code, please ignore this email or contact your administrator.') }}
        </p>
        <br>
        <div style="text-align: left;">
            <p>{{ __('Regards,') }}<br>
            <strong>{{ config('mail.from.name') }}</strong></p>
        </div>
    </div>
</body>
</html>
