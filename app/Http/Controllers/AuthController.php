<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index() {
        return view('auth.login');
    }

    public function viewDirektur() {
        return view('auth.loginDirektur');
    }

    public function viewVerifikator() {
        return view('auth.loginVerifikator');
    }

    public function viewValidator() {
        return view('auth.loginValidator');
    }

    public function viewTUK() {
        return view('auth.loginTuk');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if ($user->role === 'direktur') {
                return redirect()->route('confirm');
            } else if ($user->role === 'admin_lsp') {
                return redirect()->route('sewaktu');
            } else if ($user->role === 'validator') {
                return redirect()->route('validation');
            } else if ($user->role === 'ketua_tuk') {
                return redirect()->route('confirm_tuk');
            } else {
                return redirect()->route('verification');
            }

            // Redirect to dashboard or home if role is 'direktur'
        }

        return back()->with('error', 'Email atau password salah');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
