<?php

namespace App\Http\Controllers;

use App\Models\UserGroup;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Gate;

class UserGroupController extends Controller
{
    public function index()
    {
        Gate::authorize('view-user-groups');

        $user_groups = UserGroup::all();
        $title = "User Groups";
        return view('user_groups', compact('user_groups', 'title'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create-user-groups');

        UserGroup::create($request->validate([
            'name' => 'required|unique:user_groups,name|max:255',
            'description' => 'nullable|string',
        ]));

        return back()->with('success', 'User Group Created Successfully!');
    }

    public function update(Request $request, UserGroup $user_group)
    {
        Gate::authorize('edit-user-groups');

        $user_group->update($request->validate([
            'name' => 'required|max:255|unique:user_groups,name,' . $user_group->id,
            'description' => 'nullable|string',
        ]));

        return back()->with('success', 'User Group Updated Successfully!');
    }

    public function destroy(UserGroup $user_group)
    {
        Gate::authorize('delete-user-groups');
        
        try {
            $user_group->delete();
            return back()->with('success', 'User Group Deleted Successfully!');
        } catch (QueryException $e) {
            if ($e->getCode() == "23000") { // error code for integrity constraint violation (foreign key constraint)
                return back()->with('error', 'Cannot delete this group because users are assigned to it.');
            }
            return back()->with('error', 'Something went wrong.');
        }
    }
}
