<?php

namespace App\Http\Controllers\Backend;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('backend.users.index', compact('users'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        return view('backend.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name',
        ]);

        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        $roleNames = $request->roles;
        $roleIds = Role::whereIn('name', $roleNames)->pluck('id')->toArray();
        $user->roles()->sync($roleIds);

        return redirect()->route('admin.users.index')->with('success', 'Data user berhasil diupdate!');
    }

    public function setRole(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'role' => 'required|in:' . $user->getRoleNames()->join(','),
        ]);

        $user->active_role = $request->role;
        $user->save();

        return back()->with('success', 'Peran diubah menjadi ' . ucfirst($request->role));
    }


}