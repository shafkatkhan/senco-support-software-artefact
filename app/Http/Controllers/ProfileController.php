<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Show the profile settings form.
     */
    public function edit()
    {
        return view('profile', [
            'user' => auth()->user(),
            'title' => __('Profile Settings'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,'.$user->id,
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'mobile' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'password' => 'nullable|confirmed|min:8',
        ]);

        $user->fill([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'mobile' => $validated['mobile'] ?? '',
            'position' => $validated['position'] ?? null,
        ]);

        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('profile.edit')->with('success', __('Profile updated successfully!'));
    }
}
