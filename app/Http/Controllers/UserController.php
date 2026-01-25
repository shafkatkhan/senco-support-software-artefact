<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['group', 'addedBy'])->get();
        $user_groups = UserGroup::all();
        $title = "Users";
        return view('users', compact('users', 'user_groups', 'title'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'mobile' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'user_group_id' => 'required|exists:user_groups,id',
            'password' => 'required|string|min:8',
            'joined_date' => 'nullable|date',
            'expiry_date' => 'nullable|date',
        ]);

        User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'username' => $request->username,
            'mobile' => $request->mobile,
            'position' => $request->position,
            'user_group_id' => $request->user_group_id,
            'password' => Hash::make($request->password),
            'added_by' => auth()->id(),
            'joined_date' => $request->joined_date ?? now(),
            'expiry_date' => $request->expiry_date,
        ]);
        
        return back()->with('success', 'User Created Successfully!');
    }

    public function destroy($id)
    {
        User::destroy($id);
        return back()->with('success', 'User Deleted Successfully!');
    }
}
