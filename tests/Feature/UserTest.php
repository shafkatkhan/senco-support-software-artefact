<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_requires_authentication(): void
    {
        $response = $this->get(route('users.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_index_is_forbidden_without_permission(): void
    {
        $user = $this->userWithPermissions([]);
        $response = $this->actingAs($user)->get(route('users.index'));
        $response->assertForbidden();
    }

    public function test_index_returns_view_for_authorised_user(): void
    {
        $user = $this->viewerUser('users');
        User::factory()->create(['user_group_id' => UserGroup::factory()->create()->id]);

        $response = $this->actingAs($user)->get(route('users.index'));

        $response->assertOk();
        $response->assertViewIs('users');
        $response->assertViewHas('users');
        $response->assertViewHas('user_groups');
        $response->assertViewHas('title', 'Users');
    }

    public function test_show_requires_authentication(): void
    {
        $user = $this->userWithPermissions([]);
        $response = $this->get(route('users.show', $user));
        $response->assertRedirect(route('login'));
    }

    public function test_show_is_forbidden_without_permission(): void
    {
        $user = $this->viewerUser('users');
        $shownUser = $this->userWithPermissions([]);

        $response = $this->actingAs($user)->get(route('users.show', $shownUser));
        $response->assertForbidden();
    }

    public function test_show_returns_user_json_for_authorised_user(): void
    {
        $user = $this->userWithPermissions(['edit-users']);
        $shownUser = User::factory()->create([
            'user_group_id' => UserGroup::factory()->create()->id,
            'first_name' => 'Shown',
            'last_name' => 'User',
            'username' => 'shownuser',
        ]);

        $response = $this->actingAs($user)->get(route('users.show', $shownUser));

        $response->assertOk();
        $response->assertJsonPath('id', $shownUser->id);
        $response->assertJsonPath('first_name', 'Shown');
        $response->assertJsonPath('last_name', 'User');
        $response->assertJsonPath('username', 'shownuser');
        $response->assertJsonMissingPath('password');

    }

    public function test_store_requires_authentication(): void
    {
        $response = $this->post(route('users.store'), $this->userPayload());
        $response->assertRedirect(route('login'));
    }

    public function test_store_is_forbidden_without_permission(): void
    {
        $user = $this->viewerUser('users');
        $response = $this->actingAs($user)->post(route('users.store'), $this->userPayload());
        $response->assertForbidden();
    }

    public function test_store_creates_user(): void
    {
        $user = $this->adminUser('users');
        $group = UserGroup::factory()->create();

        $response = $this->actingAs($user)->post(route('users.store'), $this->userPayload([
            'user_group_id' => $group->id,
            'first_name' => 'New',
            'last_name' => 'User',
            'username' => 'newuser',
            'email' => 'new@example.com',
        ]));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $createdUser = User::where('username', 'newuser')->first();
        $this->assertNotNull($createdUser);
        $this->assertTrue(Hash::check('password123', $createdUser->password));
        $this->assertEquals($user->id, $createdUser->added_by);
        $this->assertDatabaseHas('users', [
            'id' => $createdUser->id,
            'first_name' => 'New',
            'last_name' => 'User',
            'email' => 'new@example.com',
            'user_group_id' => $group->id,
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $user = $this->adminUser('users');

        $response = $this->actingAs($user)->post(route('users.store'), [
            'first_name' => '',
            'last_name' => '',
            'username' => '',
            'email' => '',
            'mobile' => '',
            'user_group_id' => '',
            'password' => '',
        ]);

        $response->assertSessionHasErrors([
            'first_name',
            'last_name',
            'username',
            'email',
            'mobile',
            'user_group_id',
            'password',
        ]);
    }

    public function test_store_validates_unique_username_and_email(): void
    {
        $user = $this->adminUser('users');
        $existingUser = User::factory()->create(['user_group_id' => UserGroup::factory()->create()->id]);

        $response = $this->actingAs($user)->post(route('users.store'), $this->userPayload([
            'username' => $existingUser->username,
            'email' => $existingUser->email,
        ]));

        $response->assertSessionHasErrors(['username', 'email']);
    }

    public function test_update_requires_authentication(): void
    {
        $updatedUser = $this->userWithPermissions([]);
        $response = $this->put(route('users.update', $updatedUser), $this->userPayload());
        $response->assertRedirect(route('login'));
    }

    public function test_update_is_forbidden_without_permission(): void
    {
        $user = $this->viewerUser('users');
        $updatedUser = $this->userWithPermissions([]);

        $response = $this->actingAs($user)->put(route('users.update', $updatedUser), $this->userPayload());
        $response->assertForbidden();
    }

    public function test_update_modifies_user_without_changing_password(): void
    {
        $user = $this->adminUser('users');
        $group = UserGroup::factory()->create();
        $updatedUser = User::factory()->create([
            'user_group_id' => $group->id,
            'password' => Hash::make('oldpassword'),
        ]);
        $newGroup = UserGroup::factory()->create();

        $response = $this->actingAs($user)->put(route('users.update', $updatedUser), $this->userPayload([
            'first_name' => 'Updated',
            'last_name' => 'User',
            'username' => 'updateduser',
            'email' => 'updated@example.com',
            'user_group_id' => $newGroup->id,
            'password' => '',
        ]));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertTrue(Hash::check('oldpassword', $updatedUser->fresh()->password));
        $this->assertDatabaseHas('users', [
            'id' => $updatedUser->id,
            'first_name' => 'Updated',
            'username' => 'updateduser',
            'email' => 'updated@example.com',
            'user_group_id' => $newGroup->id,
        ]);
    }

    public function test_update_changes_password_if_provided(): void
    {
        $user = $this->adminUser('users');
        $updatedUser = $this->userWithPermissions([]);

        $response = $this->actingAs($user)->put(route('users.update', $updatedUser), $this->userPayload([
            'username' => $updatedUser->username,
            'email' => $updatedUser->email,
            'user_group_id' => $updatedUser->user_group_id,
            'password' => 'newpassword123',
        ]));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertTrue(Hash::check('newpassword123', $updatedUser->fresh()->password));
    }

    public function test_update_validates_unique_username_and_email_except_self(): void
    {
        $user = $this->adminUser('users');
        $existingUser = $this->userWithPermissions([]);
        $updatedUser = $this->userWithPermissions([]);

        $response = $this->actingAs($user)->put(route('users.update', $updatedUser), $this->userPayload([
            'username' => $existingUser->username,
            'email' => $existingUser->email,
            'user_group_id' => $updatedUser->user_group_id,
        ]));

        $response->assertSessionHasErrors(['username', 'email']);

        $response2 = $this->actingAs($user)->put(route('users.update', $updatedUser), $this->userPayload([
            'username' => $updatedUser->username,
            'email' => $updatedUser->email,
            'user_group_id' => $updatedUser->user_group_id,
        ]));

        $response2->assertSessionHasNoErrors();
    }

    public function test_destroy_requires_authentication(): void
    {
        $deletedUser = $this->userWithPermissions([]);
        $response = $this->delete(route('users.destroy', $deletedUser));
        $response->assertRedirect(route('login'));
    }

    public function test_destroy_is_forbidden_without_permission(): void
    {
        $user = $this->viewerUser('users');
        $deletedUser = $this->userWithPermissions([]);

        $response = $this->actingAs($user)->delete(route('users.destroy', $deletedUser));
        $response->assertForbidden();
    }

    public function test_destroy_deletes_user(): void
    {
        $user = $this->adminUser('users');
        $deletedUser = $this->userWithPermissions([]);

        $response = $this->actingAs($user)->delete(route('users.destroy', $deletedUser));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('users', ['id' => $deletedUser->id]);
    }

    public function test_destroy_returns_error_on_exception(): void
    {
        $user = $this->adminUser('users');
        $deletedUser = $this->userWithPermissions([]);

        User::deleting(function () {
            throw new QueryException('', '', [], new \Exception());
        });

        $response = $this->actingAs($user)->delete(route('users.destroy', $deletedUser));

        $response->assertRedirect();
        $response->assertSessionHas('error');

        User::flushEventListeners();
    }

    public function test_user_relationships_accessors_permissions_and_casts(): void
    {
        $creator = $this->userWithPermissions([]);
        $group = UserGroup::factory()->create();
        $user = User::factory()->create([
            'user_group_id' => $group->id,
            'added_by' => $creator->id,
            'first_name' => 'Test',
            'last_name' => 'User',
            'password' => 'password123',
            'joined_date' => '2026-01-01',
            'expiry_date' => '2027-01-01',
            'mfa_verified_at' => '2026-01-01 12:00:00',
        ]);

        $this->assertTrue($user->fresh()->group->is($group));
        $this->assertTrue($user->fresh()->addedBy->is($creator));
        $this->assertEquals('Test User', $user->fresh()->full_name);
        $this->assertTrue(Hash::check('password123', $user->fresh()->password));
        $this->assertInstanceOf(Carbon::class, $user->fresh()->joined_date);
        $this->assertInstanceOf(Carbon::class, $user->fresh()->expiry_date);
        $this->assertInstanceOf(Carbon::class, $user->fresh()->mfa_verified_at);
        $this->assertFalse($user->fresh()->hasPermission('view-users'));
    }

    public function test_user_can_check_permissions(): void
    {
        $user = $this->viewerUser('users');

        $this->assertTrue($user->hasPermission('view-users'));
        $this->assertFalse($user->hasPermission('delete-users'));
    }

    public function test_user_mfa_pending_state(): void
    {
        $user = $this->userWithPermissions([]);

        Setting::set('mfa_method', 'none');
        $this->assertFalse($user->isMfaPending());

        Setting::set('mfa_method', 'email');
        $this->assertTrue($user->isMfaPending());

        $user->forceFill(['mfa_verified_at' => now()])->save();
        $this->assertTrue($user->fresh()->isMfaPending());

        session(['mfa_session_verified' => true]);
        $this->assertFalse($user->fresh()->isMfaPending());
    }

    protected function userPayload(array $overrides = []): array
    {
        return array_merge([
            'first_name' => 'Test',
            'last_name' => 'User',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'mobile' => '0123456789',
            'position' => 'Teacher',
            'user_group_id' => UserGroup::factory()->create()->id,
            'password' => 'password123',
            'joined_date' => '2026-01-01',
            'expiry_date' => '2027-01-01',
        ], $overrides);
    }
}
