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
        return view('user_groups', ['title' => 'User Groups', 'user_groups' => $user_groups]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:user_groups,name|max:255',
            'description' => 'nullable|string',
        ]);

        UserGroup::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return back()->with('success', 'User Group Created Successfully!');
    }

    public function destroy($id)
    {
        $user_group = UserGroup::findOrFail($id);
        
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
