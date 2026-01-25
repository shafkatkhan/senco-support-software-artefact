<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserGroup;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['group', 'addedBy'])->get();
        $user_groups = UserGroup::all();
        $title = "Users";
        return view('users', compact('users', 'user_groups', 'title'));
    }
}
