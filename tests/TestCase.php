<?php

namespace Tests;

use App\Models\Permission;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Create a user that has the given permission slugs.
     */
    protected function userWithPermissions(array $slugs): User
    {
        $group = UserGroup::factory()->create();

        foreach ($slugs as $slug) {
            $permission = Permission::firstOrCreate([
                'slug' => $slug,
            ], [
                'name' => $slug,
                'description' => '',
            ]);
            $group->permissions()->attach($permission);
        }

        return User::factory()->create(['user_group_id' => $group->id]);
    }

    protected function viewerUser(string $resource): User
    {
        return $this->userWithPermissions(["view-{$resource}"]);
    }

    protected function adminUser(string $resource): User
    {
        return $this->userWithPermissions([
            "view-{$resource}",
            "create-{$resource}",
            "edit-{$resource}",
            "delete-{$resource}",
        ]);
    }
}
