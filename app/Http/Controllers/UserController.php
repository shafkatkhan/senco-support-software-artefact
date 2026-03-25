<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;

class UserController extends Controller
{
    public function index()
    {
        Gate::authorize('view-users');

        $users = User::with(['group', 'addedBy'])->get();
        $user_groups = UserGroup::all();
        $title = __('Users');
        return view('users', compact('users', 'user_groups', 'title'));
    }

    public function show(User $user)
    {
        Gate::authorize('edit-users');

        return response()->json($user);
    }

    public function store(Request $request)
    {
        Gate::authorize('create-users');

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
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
            'email' => $request->email,
            'mobile' => $request->mobile,
            'position' => $request->position,
            'user_group_id' => $request->user_group_id,
            'password' => Hash::make($request->password),
            'added_by' => auth()->id(),
            'joined_date' => $request->joined_date ?? now(),
            'expiry_date' => $request->expiry_date,
        ]);
        
        return back()->with('success', __(':item ":name" created successfully!', ['item' => __('User'), 'name' => $user->full_name]));
    }

    public function update(Request $request, User $user)
    {
        Gate::authorize('edit-users');

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'mobile' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'user_group_id' => 'required|exists:user_groups,id',
            'password' => 'nullable|string|min:8',
            'joined_date' => 'nullable|date',
            'expiry_date' => 'nullable|date',
        ]);

        $data = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'username' => $request->username,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'position' => $request->position,
            'user_group_id' => $request->user_group_id,
            'joined_date' => $request->joined_date,
            'expiry_date' => $request->expiry_date,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return back()->with('success', __(':item ":name" updated successfully!', ['item' => __('User'), 'name' => $user->full_name]));
    }

    public function destroy(User $user)
    {
        Gate::authorize('delete-users');
        
        try {
            $user->delete();
            return back()->with('success', __(':item ":name" deleted successfully!', ['item' => __('User'), 'name' => $user->full_name]));
        } catch (QueryException $e) {
            return back()->with('error', __('Something went wrong.'));
        }
    }
}
