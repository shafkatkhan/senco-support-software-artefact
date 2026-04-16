<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Setting;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestEmail;

class EmailSettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_requires_authentication(): void
    {
        $response = $this->get(route('email-settings.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_index_is_forbidden_without_permission(): void
    {
        $user = $this->userWithPermissions([]);
        $response = $this->actingAs($user)->get(route('email-settings.index'));
        $response->assertForbidden();
    }

    public function test_index_returns_view_for_authorised_user(): void
    {
        $user = $this->userWithPermissions(['manage-email-settings']);
        
        $response = $this->actingAs($user)->get(route('email-settings.index'));

        $response->assertOk();
        $response->assertViewIs('email_settings');
        $response->assertViewHas('settings');
    }

    public function test_update_requires_authentication(): void
    {
        $response = $this->put(route('email-settings.update'), ['mail_host' => 'smtp.test.com']);
        $response->assertRedirect(route('login'));
    }

    public function test_update_is_forbidden_without_permission(): void
    {
        $user = $this->userWithPermissions([]);
        $response = $this->actingAs($user)->put(route('email-settings.update'), ['mail_host' => 'smtp.test.com']);
        $response->assertForbidden();
    }

    public function test_update_validates_required_fields(): void
    {
        $user = $this->userWithPermissions(['manage-email-settings']);

        $response = $this->actingAs($user)->put(route('email-settings.update'), [
            'mail_host' => 'smtp.example.com',
            // missing mail_port, mail_from_address, mail_from_name
        ]);

        $response->assertSessionHasErrors(['mail_port', 'mail_from_address', 'mail_from_name']);
    }

    public function test_update_modifies_settings(): void
    {
        $user = $this->userWithPermissions(['manage-email-settings']);

        $response = $this->actingAs($user)->put(route('email-settings.update'), [
            'mail_host' => 'smtp.example.com',
            'mail_port' => 587,
            'mail_username' => 'user',
            'mail_password' => 'pass',
            'mail_encryption' => 'tls',
            'mail_from_address' => 'noreply@example.com',
            'mail_from_name' => 'Support',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals('smtp.example.com', Setting::get('mail_host'));
        $this->assertEquals('587', Setting::get('mail_port'));
        $this->assertEquals('noreply@example.com', Setting::get('mail_from_address'));
    }

    public function test_test_email_sends_email(): void
    {
        $user = $this->userWithPermissions(['manage-email-settings']);
        Mail::fake();

        $response = $this->actingAs($user)->post(route('email-settings.test'), [
            'test_email_address' => 'test@example.com'
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        Mail::assertSent(TestEmail::class, function ($mail) {
            return $mail->hasTo('test@example.com');
        });
    }

    public function test_test_email_handles_exception(): void
    {
        $user = $this->userWithPermissions(['manage-email-settings']);
        
        Mail::shouldReceive('to')->andThrow(new \Exception('Mocked connection error'));
        
        $response = $this->actingAs($user)->post(route('email-settings.test'), [
            'test_email_address' => 'test@example.com'
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }
}
