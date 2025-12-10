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
    public function index(Request $request)
    {
        $search = $request->get('search');

        // Start with base query
        $query = User::orderBy('created_at', 'desc');

        // Apply search filter if search term is provided
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('nama_tuk', 'like', '%' . $search . '%')
                  ->orWhere('role', 'like', '%' . $search . '%');
            });
        }

        $users = $query->get();
        $roles = Role::all();

        // Debug: Log search and results
        \Log::info('Users management page accessed:', [
            'search_term' => $search,
            'results_count' => $users->count()
        ]);

        // Handle AJAX request for real-time search
        if ($request->ajax() || $request->get('ajax')) {
            $html = '';
            if ($users->count() > 0) {
                foreach ($users as $index => $user) {
                    $highlightedName = $search ?
                        preg_replace('/(' . preg_quote($search, '/') . ')/i', '<mark class="bg-yellow-200">$1</mark>', $user->name) :
                        $user->name;

                    $html .= '
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm text-gray-900">' . ($index + 1) . '</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">' . $highlightedName . '</td>
                            <td class="px-6 py-4 text-sm text-gray-900">' . $user->email . '</td>
                            <td class="px-6 py-4 text-sm">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium ';

                    if($user->role === 'superadmin') $html .= 'bg-purple-100 text-purple-800';
                    elseif($user->role === 'direktur') $html .= 'bg-yellow-100 text-yellow-800';
                    elseif($user->role === 'admin_lsp') $html .= 'bg-blue-100 text-blue-800';
                    elseif($user->role === 'validator') $html .= 'bg-green-100 text-green-800';
                    elseif($user->role === 'ketua_tuk') $html .= 'bg-indigo-100 text-indigo-800';
                    elseif($user->role === 'verifikator') $html .= 'bg-red-100 text-red-800';
                    else $html .= 'bg-gray-100 text-gray-800';

                    $html .= '">' . ucfirst($user->role) . '</span></td>';
                    $html .= '<td class="px-6 py-4 text-sm text-gray-900">' . ($user->nama_tuk ?? '-') . '</td>';
                    $html .= '<td class="px-6 py-4 text-sm text-gray-900">' . ($user->notel ?? '-') . '</td>';
                    $html .= '<td class="px-6 py-4 text-sm">
                        <div class="flex space-x-2">
                            <a href="' . route('users.edit', $user->id) . '" class="inline-flex items-center px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded transition-colors">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit
                            </a>
                            <a href="' . route('users.change.password.form', $user->id) . '" class="text-yellow-600 hover:text-yellow-800 transition-colors" title="Ubah Password">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                </svg>
                            </a>';

                    if($user->id !== auth()->id() && $user->role !== 'superadmin') {
                        $html .= '
                            <form action="' . route('users.destroy', $user->id) . '" method="POST" onsubmit="return confirm(\'Apakah Anda yakin ingin menghapus user ini?\')">
                                ' . csrf_field() . '
                                ' . method_field('DELETE') . '
                                <button type="submit" class="text-red-600 hover:text-red-800 transition-colors" title="Hapus User">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>';
                    }

                    $html .= '
                        </div>
                    </td>
                </tr>';
                }
            } else {
                if ($search) {
                    $html = '
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="mb-2">Tidak ada user yang cocok dengan pencarian "<strong>' . $search . '</strong>"</p>
                                <a href="' . route('users.manage') . '" class="text-blue-600 hover:text-blue-800 font-medium">Hapus pencarian</a>
                            </td>
                        </tr>';
                } else {
                    $html = '
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                <p>Belum ada user yang terdaftar</p>
                                <a href="' . route('register.new') . '" class="text-blue-600 hover:text-blue-800 font-medium">Tambah user baru</a>
                            </td>
                        </tr>';
                }
            }

            return response()->json([
                'html' => $html,
                'count' => $users->count()
            ]);
        }

        return view('auth.manage-users', compact('users', 'roles', 'search'));
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
