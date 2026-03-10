<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Permission;
use App\Models\UserGroup;

class PermissionController extends Controller
{
    public function index()
    {
        Gate::authorize('manage-permissions');

        $permissions = Permission::all();
        $userGroups = UserGroup::with('permissions')->get();
        $title = 'Roles & Permissions';
        return view('permissions', compact('permissions', 'userGroups', 'title'));
    }

    // submits permissions as permissions[user_group_id][] = permission_id
    public function update(Request $request)
    {
        Gate::authorize('manage-permissions');

        $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*.*' => 'exists:permissions,id',
        ]);
        $inputPermissions = $request->input('permissions', []);

        foreach (UserGroup::all() as $userGroup) {
            $groupPermissions = $inputPermissions[$userGroup->id] ?? [];
            $userGroup->permissions()->sync($groupPermissions);
        }

        return back()->with('success', __('Permissions updated successfully!'));
    }
}
