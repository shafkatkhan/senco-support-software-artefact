<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Setting;
use App\Models\User;

class MfaSettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_requires_authentication(): void
    {
        $response = $this->get(route('mfa-settings.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_index_is_forbidden_without_permission(): void
    {
        $user = $this->userWithPermissions([]);
        $response = $this->actingAs($user)->get(route('mfa-settings.index'));
        $response->assertForbidden();
    }

    public function test_index_returns_view_for_authorised_user(): void
    {
        $user = $this->userWithPermissions(['manage-mfa-settings']);
        
        $response = $this->actingAs($user)->get(route('mfa-settings.index'));

        $response->assertOk();
        $response->assertViewIs('mfa_settings');
        $response->assertViewHas('mfa_method');
    }

    public function test_update_requires_authentication(): void
    {
        $response = $this->put(route('mfa-settings.update'), ['mfa_method' => 'none']);
        $response->assertRedirect(route('login'));
    }

    public function test_update_is_forbidden_without_permission(): void
    {
        $user = $this->userWithPermissions([]);
        $response = $this->actingAs($user)->put(route('mfa-settings.update'), ['mfa_method' => 'none']);
        $response->assertForbidden();
    }

    public function test_update_prevents_email_mfa_if_smtp_not_configured(): void
    {
        $user = $this->userWithPermissions(['manage-mfa-settings']);
        Setting::where('key', 'mail_host')->delete(); // ensure not set

        $response = $this->actingAs($user)->put(route('mfa-settings.update'), [
            'mfa_method' => 'email',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_update_modifies_settings_and_clears_user_mfa_secrets_if_method_changed(): void
    {
        $user = $this->userWithPermissions(['manage-mfa-settings']);
        Setting::set('mfa_method', 'none');
        Setting::set('mail_host', 'smtp.example.com'); // to allow email mfa
        
        $group = \App\Models\UserGroup::factory()->create();
        $testUser = User::factory()->create([
            'user_group_id' => $group->id,
            'mfa_secret' => 'SECRET',
            'mfa_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->put(route('mfa-settings.update'), [
            'mfa_method' => 'email',
        ]);

        $response->assertRedirect(route('mfa-settings.index'));
        $response->assertSessionHas('success');

        $this->assertEquals('email', Setting::get('mfa_method'));
        
        // check if user's secret was cleared
        $this->assertNull($testUser->fresh()->mfa_secret);
        $this->assertNull($testUser->fresh()->mfa_verified_at);
    }
}
