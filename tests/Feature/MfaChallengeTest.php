<?php

namespace Tests\Feature;

use App\Mail\EmailMfaCode;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

class MfaChallengeTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_requires_authentication(): void
    {
        $response = $this->get(route('mfa-challenge.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_index_redirects_when_challenge_is_not_needed(): void
    {
        $user = $this->userWithPermissions([]);
        Setting::set('mfa_method', 'none');

        $response = $this->actingAs($user)->get(route('mfa-challenge.index'));

        $response->assertRedirect('/');
    }

    public function test_index_redirects_to_setup_when_user_has_not_completed_mfa(): void
    {
        $user = $this->userWithPermissions([]);
        Setting::set('mfa_method', 'email');

        $response = $this->actingAs($user)->get(route('mfa-challenge.index'));

        $response->assertRedirect(route('mfa-setup.index'));
    }

    public function test_index_sends_email_challenge_code(): void
    {
        Mail::fake();
        $user = $this->userWithPermissions([]);
        $user->forceFill(['mfa_verified_at' => now()])->save();
        Setting::set('mfa_method', 'email');

        $response = $this->actingAs($user)->get(route('mfa-challenge.index'));

        $response->assertOk();
        $response->assertViewIs('mfa_challenge');
        $response->assertViewHas('mfa_method', 'email');
        $this->assertNotNull(Cache::get('mfa_challenge_code_' . $user->id));
        Mail::assertSent(EmailMfaCode::class);
    }

    public function test_index_returns_error_when_challenge_email_fails(): void
    {
        $user = $this->userWithPermissions([]);
        $user->forceFill(['mfa_verified_at' => now()])->save();
        Setting::set('mfa_method', 'email');
        Mail::shouldReceive('to')->andThrow(new \Exception('SMTP unavailable'));

        $response = $this->actingAs($user)->get(route('mfa-challenge.index'));

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_index_does_not_resend_email_challenge_when_rate_limited(): void
    {
        Mail::fake();
        $user = $this->userWithPermissions([]);
        $user->forceFill(['mfa_verified_at' => now()])->save();
        Setting::set('mfa_method', 'email');
        Cache::put('mfa_email_sent_' . $user->id, true, now()->addMinute());

        $response = $this->actingAs($user)->get(route('mfa-challenge.index'));

        $response->assertOk();
        $this->assertNull(Cache::get('mfa_challenge_code_' . $user->id));
        Mail::assertNothingSent();
    }

    public function test_verify_validates_pin(): void
    {
        $user = $this->userWithPermissions([]);

        $response = $this->actingAs($user)->post(route('mfa-challenge.verify'), [
            'pin' => '123',
        ]);

        $response->assertSessionHasErrors('pin');
    }

    public function test_verify_accepts_authenticator_code(): void
    {
        $google2fa = new Google2FA();
        $secret = $google2fa->generateSecretKey();
        $user = $this->userWithPermissions([]);
        $user->forceFill([
            'mfa_secret' => $secret,
            'mfa_verified_at' => now(),
        ])->save();
        Setting::set('mfa_method', 'authenticator_app');

        $response = $this->actingAs($user)->post(route('mfa-challenge.verify'), [
            'pin' => $google2fa->getCurrentOtp($secret),
        ]);

        $response->assertRedirect('/');
        $this->assertTrue(session('mfa_session_verified'));
    }

    public function test_verify_accepts_email_code_and_clears_cache(): void
    {
        $user = $this->userWithPermissions([]);
        $user->forceFill(['mfa_verified_at' => now()])->save();
        Setting::set('mfa_method', 'email');
        Cache::put('mfa_challenge_code_' . $user->id, '123456', now()->addMinutes(15));

        $response = $this->actingAs($user)->post(route('mfa-challenge.verify'), [
            'pin' => '123456',
        ]);

        $response->assertRedirect('/');
        $this->assertTrue(session('mfa_session_verified'));
        $this->assertNull(Cache::get('mfa_challenge_code_' . $user->id));
    }

    public function test_verify_rejects_invalid_code(): void
    {
        $user = $this->userWithPermissions([]);
        $user->forceFill(['mfa_verified_at' => now()])->save();
        Setting::set('mfa_method', 'email');
        Cache::put('mfa_challenge_code_' . $user->id, '123456', now()->addMinutes(15));

        $response = $this->actingAs($user)->post(route('mfa-challenge.verify'), [
            'pin' => '654321',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertFalse(session('mfa_session_verified', false));
    }
}
