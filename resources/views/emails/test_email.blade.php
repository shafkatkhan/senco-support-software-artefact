<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Test Email</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 5px; text-align: center; margin-bottom: 20px;">
        <h2 style="color: #0c3674; margin-top: 0;">{{ __('Test Email') }}</h2>
        <p style="font-size: 16px; margin-bottom: 0;">{{ __('This is a test email sent from EduSen.') }}</p>
    </div>
    <div style="padding: 20px; border: 1px solid #dee2e6; border-radius: 5px;">
        <p>{{ __('Hello,') }}</p>
        <p>{{ __('If you are reading this, it means your SMTP configuration is working correctly!') }}</p>
        <p>{{ __('You can safely ignore this email.') }}</p>
        <br>
        <p>{{ __('Regards,') }}<br>
        <strong>{{ config('mail.from.name') }}</strong></p>
    </div>
</body>
</html>