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

        // Debug: Log all users
        \Log::info('All users in manage page:');
        foreach ($users as $user) {
            \Log::info("User ID: {$user->id}, Name: {$user->name}, Role: {$user->role}");
        }

        return view('auth.manage-users', compact('users', 'roles'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();

        // Debug logging
        \Log::info('Edit user accessed:', [
            'editing_user_id' => $id,
            'editing_user_role' => $user->role,
            'current_user_id' => auth()->id(),
            'current_user_role' => auth()->user()->role
        ]);

        // Only superadmin can edit users
        if (auth()->user()->role !== 'superadmin') {
            abort(403, 'Unauthorized access - Only superadmin can edit users. Your role: ' . auth()->user()->role);
        }

        return view('auth.edit-user', compact('user', 'roles'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Debug logging
        \Log::info('Update user attempt:', [
            'updating_user_id' => $id,
            'updating_user_role' => $user->role,
            'current_user_id' => auth()->id(),
            'current_user_role' => auth()->user()->role,
            'request_data' => $request->all()
        ]);

        // Only superadmin can edit users
        if (auth()->user()->role !== 'superadmin') {
            abort(403, 'Unauthorized access - Only superadmin can edit users. Your role: ' . auth()->user()->role);
        }

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,'.$id,
                'role' => 'required|string|exists:roles,role',
                'password' => 'nullable|min:6',
                'nama_tuk' => 'nullable|string|max:255',
                'notel' => 'nullable|string|max:20',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed:', [
                'errors' => $e->errors(),
                'request_data' => $request->all(),
                'user_id' => $id,
                'user_role' => $user->role
            ]);
            throw $e;
        }

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

        // Only superadmin can change passwords
        if (auth()->user()->role !== 'superadmin') {
            abort(403, 'Unauthorized access - Only superadmin can change passwords');
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

        // Only superadmin can change passwords
        if (auth()->user()->role !== 'superadmin') {
            abort(403, 'Unauthorized access - Only superadmin can change passwords');
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('users.manage')
            ->with('success', 'Password user berhasil diubah!');
    }
}
