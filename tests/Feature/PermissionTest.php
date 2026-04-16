<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\UserGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_requires_authentication(): void
    {
        $response = $this->get(route('permissions.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_index_is_forbidden_without_permission(): void
    {
        $user = $this->userWithPermissions([]);
        $response = $this->actingAs($user)->get(route('permissions.index'));
        $response->assertForbidden();
    }

    public function test_index_returns_view_for_authorised_user(): void
    {
        $user = $this->userWithPermissions(['manage-permissions']);
        
        $response = $this->actingAs($user)->get(route('permissions.index'));

        $response->assertOk();
        $response->assertViewIs('permissions');
        $response->assertViewHas('permissions');
        $response->assertViewHas('userGroups');
    }

    public function test_update_requires_authentication(): void
    {
        $response = $this->post(route('permissions.update'), []);
        $response->assertRedirect(route('login'));
    }

    public function test_update_is_forbidden_without_permission(): void
    {
        $user = $this->userWithPermissions([]);
        $response = $this->actingAs($user)->post(route('permissions.update'), []);
        $response->assertForbidden();
    }

    public function test_update_modifies_group_permissions(): void
    {
        $user = $this->userWithPermissions(['manage-permissions']);
        $group = UserGroup::factory()->create();
        $perm = Permission::create(['name' => 'view-test', 'slug' => 'view-test', 'description' => 'Test']);

        $this->assertFalse($group->permissions->contains($perm));

        $response = $this->actingAs($user)->post(route('permissions.update'), [
            'permissions' => [
                $group->id => [$perm->id]
            ],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertTrue($group->fresh()->permissions->contains($perm));
    }
}
