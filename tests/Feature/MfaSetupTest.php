<?php

namespace Tests\Feature;

use App\Mail\EmailMfaCode;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

class MfaSetupTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_requires_authentication(): void
    {
        $response = $this->get(route('mfa-setup.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_index_returns_view_when_mfa_is_disabled(): void
    {
        $user = $this->userWithPermissions([]);
        Setting::set('mfa_method', 'none');

        $response = $this->actingAs($user)->get(route('mfa-setup.index'));

        $response->assertOk();
        $response->assertViewIs('mfa_setup');
        $response->assertViewHas('mfa_method', 'none');
        $response->assertViewHas('qrCodeSvg', null);
        $response->assertViewHas('mfaSecret', null);
    }

    public function test_index_generates_authenticator_secret_and_qr_code(): void
    {
        $user = $this->userWithPermissions([]);
        Setting::set('mfa_method', 'authenticator_app');

        $response = $this->actingAs($user)->get(route('mfa-setup.index'));

        $response->assertOk();
        $response->assertViewIs('mfa_setup');
        $response->assertViewHas('mfa_method', 'authenticator_app');
        $response->assertViewHas('qrCodeSvg');
        $response->assertViewHas('mfaSecret');
        $this->assertNotNull($user->fresh()->mfa_secret);
    }

    public function test_index_sends_email_code_when_email_mfa_is_enabled(): void
    {
        Mail::fake();
        $user = $this->userWithPermissions([]);
        Setting::set('mfa_method', 'email');

        $response = $this->actingAs($user)->get(route('mfa-setup.index'));

        $response->assertOk();
        $response->assertViewIs('mfa_setup');
        $this->assertNotNull(Cache::get('mfa_setup_code_' . $user->id));
        Mail::assertSent(EmailMfaCode::class);
    }

    public function test_index_returns_error_when_setup_email_fails(): void
    {
        $user = $this->userWithPermissions([]);
        Setting::set('mfa_method', 'email');
        Mail::shouldReceive('to')->andThrow(new \Exception('SMTP unavailable'));

        $response = $this->actingAs($user)->get(route('mfa-setup.index'));

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_index_does_not_resend_email_code_when_rate_limited(): void
    {
        Mail::fake();
        $user = $this->userWithPermissions([]);
        Setting::set('mfa_method', 'email');
        Cache::put('mfa_email_sent_' . $user->id, true, now()->addMinute());

        $response = $this->actingAs($user)->get(route('mfa-setup.index'));

        $response->assertOk();
        $this->assertNull(Cache::get('mfa_setup_code_' . $user->id));
        Mail::assertNothingSent();
    }

    public function test_verify_validates_pin(): void
    {
        $user = $this->userWithPermissions([]);

        $response = $this->actingAs($user)->post(route('mfa-setup.verify'), [
            'pin' => '123',
        ]);

        $response->assertSessionHasErrors('pin');
    }

    public function test_verify_accepts_authenticator_code(): void
    {
        $google2fa = new Google2FA();
        $secret = $google2fa->generateSecretKey();
        $user = $this->userWithPermissions([]);
        $user->forceFill(['mfa_secret' => $secret])->save();
        Setting::set('mfa_method', 'authenticator_app');

        $response = $this->actingAs($user)->post(route('mfa-setup.verify'), [
            'pin' => $google2fa->getCurrentOtp($secret),
        ]);

        $response->assertRedirect(route('mfa-setup.index'));
        $response->assertSessionHas('success');
        $this->assertNotNull($user->fresh()->mfa_verified_at);
        $this->assertTrue(session('mfa_session_verified'));
    }

    public function test_verify_accepts_email_code_and_clears_cache(): void
    {
        $user = $this->userWithPermissions([]);
        Setting::set('mfa_method', 'email');
        Cache::put('mfa_setup_code_' . $user->id, '123456', now()->addMinutes(15));

        $response = $this->actingAs($user)->post(route('mfa-setup.verify'), [
            'pin' => '123456',
        ]);

        $response->assertRedirect(route('mfa-setup.index'));
        $response->assertSessionHas('success');
        $this->assertNotNull($user->fresh()->mfa_verified_at);
        $this->assertNull(Cache::get('mfa_setup_code_' . $user->id));
    }

    public function test_verify_rejects_invalid_code(): void
    {
        $user = $this->userWithPermissions([]);
        Setting::set('mfa_method', 'email');
        Cache::put('mfa_setup_code_' . $user->id, '123456', now()->addMinutes(15));

        $response = $this->actingAs($user)->post(route('mfa-setup.verify'), [
            'pin' => '654321',
        ]);

        $response->assertRedirect(route('mfa-setup.index'));
        $response->assertSessionHas('error');
        $this->assertNull($user->fresh()->mfa_verified_at);
    }

    public function test_reset_clears_mfa_details(): void
    {
        $user = $this->userWithPermissions([]);
        $user->forceFill([
            'mfa_secret' => 'SECRET',
            'mfa_verified_at' => now(),
        ])->save();

        $response = $this->actingAs($user)
            ->withSession(['mfa_session_verified' => true])
            ->post(route('mfa-setup.reset'));

        $response->assertRedirect(route('mfa-setup.index'));
        $response->assertSessionHas('success');
        $this->assertNull($user->fresh()->mfa_secret);
        $this->assertNull($user->fresh()->mfa_verified_at);
        $this->assertFalse(session()->has('mfa_session_verified'));
    }
}
