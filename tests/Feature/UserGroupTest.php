<?php

namespace Tests\Feature;

use App\Models\UserGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserGroupTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_requires_authentication(): void
    {
        $response = $this->get(route('user-groups.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_index_is_forbidden_without_permission(): void
    {
        $user = $this->userWithPermissions([]);
        $response = $this->actingAs($user)->get(route('user-groups.index'));
        $response->assertForbidden();
    }

    public function test_index_returns_view_for_authorised_user(): void
    {
        $user = $this->viewerUser('user-groups');

        $response = $this->actingAs($user)->get(route('user-groups.index'));

        $response->assertOk();
        $response->assertViewIs('user_groups');
        $response->assertViewHas('user_groups');
    }

    public function test_store_requires_authentication(): void
    {
        $response = $this->post(route('user-groups.store'), ['name' => 'Test']);
        $response->assertRedirect(route('login'));
    }

    public function test_store_is_forbidden_without_permission(): void
    {
        $user = $this->viewerUser('user-groups');
        $response = $this->actingAs($user)->post(route('user-groups.store'), ['name' => 'Test']);
        $response->assertForbidden();
    }

    public function test_store_creates_user_group(): void
    {
        $user = $this->adminUser('user-groups');

        $response = $this->actingAs($user)->post(route('user-groups.store'), [
            'name' => 'Admins',
            'description' => 'System Administrators',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('user_groups', [
            'name' => 'Admins',
            'description' => 'System Administrators',
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $user = $this->adminUser('user-groups');

        $response = $this->actingAs($user)->post(route('user-groups.store'), [
            'name' => '',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_update_requires_authentication(): void
    {
        $group = UserGroup::factory()->create();
        $response = $this->put(route('user-groups.update', $group), ['name' => 'Changed']);
        $response->assertRedirect(route('login'));
    }

    public function test_update_is_forbidden_without_permission(): void
    {
        $user = $this->viewerUser('user-groups');
        $group = UserGroup::factory()->create();
        
        $response = $this->actingAs($user)->put(route('user-groups.update', $group), ['name' => 'Changed']);
        $response->assertForbidden();
    }

    public function test_update_modifies_user_group(): void
    {
        $user = $this->adminUser('user-groups');
        $group = UserGroup::factory()->create(['name' => 'Old', 'description' => 'Old']);

        $response = $this->actingAs($user)->put(route('user-groups.update', $group), [
            'name' => 'New Name',
            'description' => 'New Desc',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('user_groups', [
            'id' => $group->id,
            'name' => 'New Name',
            'description' => 'New Desc',
        ]);
    }

    public function test_destroy_requires_authentication(): void
    {
        $group = UserGroup::factory()->create();
        $response = $this->delete(route('user-groups.destroy', $group));
        $response->assertRedirect(route('login'));
    }

    public function test_destroy_is_forbidden_without_permission(): void
    {
        $user = $this->viewerUser('user-groups');
        $group = UserGroup::factory()->create();
        
        $response = $this->actingAs($user)->delete(route('user-groups.destroy', $group));
        $response->assertForbidden();
    }

    public function test_destroy_deletes_user_group(): void
    {
        $user = $this->adminUser('user-groups');
        $group = UserGroup::factory()->create();

        $response = $this->actingAs($user)->delete(route('user-groups.destroy', $group));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('user_groups', ['id' => $group->id]);
    }

    public function test_destroy_returns_error_on_fk_constraint(): void
    {
        $user = $this->adminUser('user-groups');
        $group = UserGroup::factory()->create();

        UserGroup::deleting(function () {
            throw new \Illuminate\Database\QueryException('', '', [], new \Exception('fk', 23000));
        });

        $response = $this->actingAs($user)->delete(route('user-groups.destroy', $group));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        
        UserGroup::flushEventListeners();
    }

    public function test_destroy_returns_error_on_general_exception(): void
    {
        $user = $this->adminUser('user-groups');
        $group = UserGroup::factory()->create();

        UserGroup::deleting(function () {
            throw new \Illuminate\Database\QueryException('', '', [], new \Exception());
        });

        $response = $this->actingAs($user)->delete(route('user-groups.destroy', $group));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        
        UserGroup::flushEventListeners();
    }
}
