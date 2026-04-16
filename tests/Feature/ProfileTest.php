<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_edit_requires_authentication(): void
    {
        $response = $this->get(route('profile.edit'));
        $response->assertRedirect(route('login'));
    }

    public function test_edit_returns_view_for_authenticated_user(): void
    {
        $group = \App\Models\UserGroup::factory()->create();
        $user = User::factory()->create(['user_group_id' => $group->id]);

        $response = $this->actingAs($user)->get(route('profile.edit'));

        $response->assertOk();
        $response->assertViewIs('profile');
        $response->assertViewHas('user');
    }

    public function test_update_requires_authentication(): void
    {
        $response = $this->put(route('profile.update'), []);
        $response->assertRedirect(route('login'));
    }

    public function test_update_validates_required_fields(): void
    {
        $group = \App\Models\UserGroup::factory()->create();
        $user = User::factory()->create(['user_group_id' => $group->id]);

        $response = $this->actingAs($user)->put(route('profile.update'), []);

        $response->assertSessionHasErrors(['first_name', 'last_name', 'username', 'email']);
    }

    public function test_update_changes_profile_information(): void
    {
        $group = \App\Models\UserGroup::factory()->create();
        $user = User::factory()->create([
            'user_group_id' => $group->id,
            'first_name' => 'Old',
            'last_name' => 'Name',
        ]);

        $response = $this->actingAs($user)->put(route('profile.update'), [
            'first_name' => 'New',
            'last_name' => 'Name',
            'username' => 'newuser',
            'email' => 'new@example.com',
            'mobile' => '1234567890',
            'position' => 'Teacher',
        ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'first_name' => 'New',
            'username' => 'newuser',
            'email' => 'new@example.com',
            'mobile' => '1234567890',
            'position' => 'Teacher',
        ]);
    }

    public function test_update_validates_unique_email_and_username_except_self(): void
    {
        $group = \App\Models\UserGroup::factory()->create();
        $user1 = User::factory()->create([
            'user_group_id' => $group->id,
            'username' => 'existing_user',
            'email' => 'existing@example.com'
        ]);

        $user2 = User::factory()->create(['user_group_id' => $group->id]);

        $response = $this->actingAs($user2)->put(route('profile.update'), [
            'first_name' => 'Test',
            'last_name' => 'User',
            'username' => 'existing_user',
            'email' => 'existing@example.com',
        ]);

        $response->assertSessionHasErrors(['username', 'email']);
        
        // Test ignoring self
        $response2 = $this->actingAs($user1)->put(route('profile.update'), [
            'first_name' => 'Test',
            'last_name' => 'User',
            'username' => 'existing_user',
            'email' => 'existing@example.com',
        ]);

        $response2->assertSessionHasNoErrors();
    }

    public function test_update_updates_password_if_provided(): void
    {
        $group = \App\Models\UserGroup::factory()->create();
        $user = User::factory()->create([
            'user_group_id' => $group->id,
            'password' => Hash::make('oldpassword'),
        ]);

        $response = $this->actingAs($user)->put(route('profile.update'), [
            'first_name' => 'Test',
            'last_name' => 'User',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success');

        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
    }
}
