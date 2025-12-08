<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserManagementController extends Controller
{
    /**
     * Display a listing of all users for management.
     */
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        $roles = Role::all();

        return view('auth.manage-users', compact('users', 'roles'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();

        // Prevent editing superadmin unless the current user is also superadmin
        if ($user->role === 'superadmin' && auth()->user()->role !== 'superadmin') {
            abort(403, 'Unauthorized access');
        }

        return view('auth.edit-user', compact('user', 'roles'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Prevent editing superadmin unless the current user is also superadmin
        if ($user->role === 'superadmin' && auth()->user()->role !== 'superadmin') {
            abort(403, 'Unauthorized access');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$id,
            'role' => 'required|string|exists:roles,role',
            'password' => 'nullable|min:6',
            'nama_tuk' => 'nullable|string|max:255',
            'notel' => 'nullable|string|max:20',
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'nama_tuk' => $request->nama_tuk,
            'notel' => $request->notel,
        ];

        // Update password if provided
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return redirect()->route('users.manage')
            ->with('success', 'User berhasil diperbarui!');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Prevent deleting self or superadmin
        if ($user->id === auth()->id()) {
            return redirect()->route('users.manage')
                ->with('error', 'Tidak dapat menghapus akun sendiri!');
        }

        if ($user->role === 'superadmin') {
            return redirect()->route('users.manage')
                ->with('error', 'Tidak dapat menghapus akun superadmin!');
        }

        $user->delete();

        return redirect()->route('users.manage')
            ->with('success', 'User berhasil dihapus!');
    }

    /**
     * Show the form for changing user password.
     */
    public function changePasswordForm($id)
    {
        $user = User::findOrFail($id);

        // Prevent changing superadmin password unless the current user is also superadmin
        if ($user->role === 'superadmin' && auth()->user()->role !== 'superadmin') {
            abort(403, 'Unauthorized access');
        }

        return view('auth.change-password', compact('user'));
    }

    /**
     * Process the password change.
     */
    public function changePassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::findOrFail($id);

        // Prevent changing superadmin password unless the current user is also superadmin
        if ($user->role === 'superadmin' && auth()->user()->role !== 'superadmin') {
            abort(403, 'Unauthorized access');
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('users.manage')
            ->with('success', 'Password user berhasil diubah!');
    }
}
