<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class RegistrationController extends Controller
{
    /**
     * Display registration form
     */
    public function index()
    {
        // Get all roles from database
        $roles = DB::table('roles')->get();

        return view('auth.register-new', compact('roles'));
    }

    /**
     * Store new user registration
     */
    public function store(Request $request)
    {
        // Validate request
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'nama_tuk' => 'nullable|string|max:255',
            'notel' => 'nullable|string|max:20',
            'role' => 'required|exists:roles,role',
        ]);

        try {
            // Get the original password (before hashing)
            $originalPassword = $request->password;

            // Store user data in session for success page
            Session::flash('registration_data', [
                'name' => $request->name,
                'email' => $request->email,
                'password' => $originalPassword, // Store original password
                'nama_tuk' => $request->nama_tuk,
                'notel' => $request->notel,
                'role' => $request->role,
            ]);

            // Create new user with hashed password
            DB::table('users')->insert([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password), // Hash the password for database
                'nama_tuk' => $request->nama_tuk,
                'notel' => $request->notel,
                'role' => $request->role,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->route('registration.success')->with('success', 'User registered successfully!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Registration failed: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display registration success page with Excel-like format
     */
    public function success()
    {
        // Get registration data from session
        $registrationData = Session::get('registration_data');

        if (!$registrationData) {
            return redirect()->route('register.new');
        }

        return view('auth.registration-success', compact('registrationData'));
    }
}