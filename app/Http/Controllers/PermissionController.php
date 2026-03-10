<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permission;
use App\Models\UserGroup;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::all();
        $userGroups = UserGroup::with('permissions')->get();
        $title = 'Roles & Permissions';
        return view('permissions', compact('permissions', 'userGroups', 'title'));
    }

    // submits permissions as permissions[user_group_id][] = permission_id
    public function update(Request $request)
    {
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
