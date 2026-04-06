<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Traits\Loggable; // <-- tambahkan ini

class AuthController extends Controller
{
    use Loggable; // <-- gunakan trait

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

            $user = Auth::user();

            // Catat log login
            $this->function_log('Auth', 'login', 'User ' . $user->name . ' login');

            if ($user->hasRole('Superadmin')) {
                return redirect()->route('dashboard')->with('success', 'Login berhasil!');
            } elseif ($user->hasRole('Adminfutsal')) {
                return redirect()->route('adminfutsal.dashboard')->with('success', 'Login berhasil!');
            } elseif ($user->hasRole('Adminkantin')) {
                return redirect()->route('adminkantin.dashboard')->with('success', 'Login berhasil!');
            } elseif ($user->hasRole('Adminac')) {
                return redirect()->route('adminac.dashboard')->with('success', 'Login berhasil!');
            } elseif ($user->hasRole('Teknisi')) {
                return redirect()->route('teknisi.dashboard')->with('success', 'Login berhasil!');
            }

            return redirect()->intended('/dashboard')->with('success', 'Login berhasil!');
        }

        return back()->with('error', 'Email atau password salah.')->withInput($request->only('email'));
    }
    public function logout(Request $request)
    {
        // Catat log logout sebelum session dihapus
        if (Auth::check()) {
            $this->function_log('Auth', 'logout', 'User ' . Auth::user()->name . ' logout');
        }

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

        // Catat log register
        $this->function_log('Auth', 'register', 'User baru terdaftar: ' . $user->name);

        return redirect('/dashboard')->with('success', 'Akun berhasil dibuat!');
    }
}
