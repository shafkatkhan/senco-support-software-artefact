<?php

namespace Tests\Feature;

use App\Mail\EmailMfaCode;
use App\Mail\TestEmail;
use Tests\TestCase;

class MailTest extends TestCase
{
    public function test_test_email_has_expected_envelope_content_and_no_attachments(): void
    {
        $mail = new TestEmail();

        $this->assertEquals('Test Email - EduSen', $mail->envelope()->subject);
        $this->assertEquals('emails.test_email', $mail->content()->view);
        $this->assertEquals([], $mail->attachments());

        $mail->assertSeeInHtml('Test Email');
        $mail->assertSeeInHtml('SMTP configuration is working correctly');
    }

    public function test_email_mfa_code_has_expected_envelope_content_code_and_no_attachments(): void
    {
        $mail = new EmailMfaCode('123456');

        $this->assertEquals('Your MFA Verification Code', $mail->envelope()->subject);
        $this->assertEquals('emails.mfa_code', $mail->content()->view);
        $this->assertEquals([], $mail->attachments());
        $this->assertEquals('123456', $mail->code);

        $mail->assertSeeInHtml('123456');
        $mail->assertSeeInHtml('This code will expire in 15 minutes.');
    }
}
