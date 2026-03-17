<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class AuthController extends Controller
{
    // ── LOGIN ─────────────────────────────────────────────

    public function showLogin()
    {
        if (Auth::check()) {
            return redirect('/dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();

            $role = DB::table('roles_users')
                ->join('roles', 'roles.id', '=', 'roles_users.role_id')
                ->where('roles_users.user_id', auth()->id())
                ->value('roles.nama');

            return match(strtolower($role ?? '')) {
                'superadmin'  => redirect()->route('dashboard'),
                'adminfutsal' => redirect()->route('adminfutsal.dashboard'),
                'adminkantin' => redirect()->route('dashboard'), // sesuaikan nanti
                'adminac'     => redirect()->route('dashboard'), // sesuaikan nanti
                default       => redirect()->route('dashboard'),
            };
        }

        return back()->with('error', 'Email atau password salah.')->withInput($request->only('email'));
    }
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    // ── REGISTER ──────────────────────────────────────────

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect('/dashboard');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        return redirect('/dashboard')->with('success', 'Akun berhasil dibuat!');
    }
}
