<?php

namespace App\Http\Controllers;

use App\Models\UserGroup;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class UserGroupController extends Controller
{
    public function index()
    {
        $user_groups = UserGroup::all();
        $title = "User Groups";
        return view('user_groups', compact('user_groups', 'title'));
    }

    public function store(Request $request)
    {
        UserGroup::create($request->validate([
            'name' => 'required|unique:user_groups,name|max:255',
            'description' => 'nullable|string',
        ]));

        return back()->with('success', 'User Group Created Successfully!');
    }

    public function update(Request $request, UserGroup $user_group)
    {
        $user_group->update($request->validate([
            'name' => 'required|max:255|unique:user_groups,name,' . $user_group->id,
            'description' => 'nullable|string',
        ]));

        return back()->with('success', 'User Group Updated Successfully!');
    }

    public function destroy(UserGroup $user_group)
    {
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
