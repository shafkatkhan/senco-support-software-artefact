<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_validates_required_fields(): void
    {
        $response = $this->post('/login', []);

        $response->assertSessionHasErrors(['username', 'password']);
        $this->assertGuest();
    }

    public function test_login_rejects_invalid_credentials(): void
    {
        $group = UserGroup::factory()->create();
        $user = User::factory()->create([
            'user_group_id' => $group->id,
            'username' => 'testuser',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/login', [
            'username' => $user->username,
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Username or password is incorrect.',
            'message2' => 'Please try again.',
        ]);
        $this->assertGuest();
    }

    public function test_login_rejects_expired_user(): void
    {
        $group = UserGroup::factory()->create();
        $user = User::factory()->create([
            'user_group_id' => $group->id,
            'username' => 'testuser',
            'password' => Hash::make('password'),
            'expiry_date' => now()->subDay()->format('Y-m-d'),
        ]);

        $response = $this->post('/login', [
            'username' => $user->username,
            'password' => 'password',
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'This user account has expired.',
            'message2' => 'Please contact your administrator.',
        ]);
        $this->assertGuest();
    }

    public function test_login_authenticates_valid_user(): void
    {
        $group = UserGroup::factory()->create();
        $user = User::factory()->create([
            'user_group_id' => $group->id,
            'username' => 'testuser',
            'password' => Hash::make('password'),
            'expiry_date' => now()->addDay()->format('Y-m-d'),
        ]);

        $response = $this->post('/login', [
            'username' => $user->username,
            'password' => 'password',
        ]);

        $response->assertOk();
        $response->assertContent('"success"');
        $this->assertAuthenticatedAs($user);
    }

    public function test_logout_logs_out_user_and_redirects_to_login(): void
    {
        $group = UserGroup::factory()->create();
        $user = User::factory()->create(['user_group_id' => $group->id]);

        $response = $this->actingAs($user)->post(route('logout'));

        $response->assertRedirect('/login');
        $this->assertGuest();
    }
}
