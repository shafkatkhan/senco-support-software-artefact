<?php

namespace App\Http\Controllers;

use App\Models\UserGroup;
use Illuminate\Http\Request;

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
}
