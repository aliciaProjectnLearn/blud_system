<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Traits\Loggable;

class AuthController extends Controller
{
    use Loggable;

    // ── LOGIN ─────────────────────────────────────────────

    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        // Jika rute adalah /login (default), redirect ke home (pelanggan tidak login)
        if (request()->is('login')) {
            return redirect()->route('home');
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

            $roleName = strtolower($role ?? '');
            
            // Redirect based on the NEW route structure (STEP 2)
            $dashboardRoute = route('home');
            
            if ($roleName === 'superadmin') {
                $dashboardRoute = route('admin.dashboard');
            } elseif (in_array($roleName, ['adminfutsal', 'adminkantin', 'adminac', 'adminservis'])) {
                // Semua admin mengarah ke prefix admin.[sub]
                $sub = str_replace('admin', '', $roleName);
                if ($roleName === 'adminservis') $sub = 'servis';
                $dashboardRoute = route("admin.$sub.dashboard");
            } elseif ($roleName === 'teknisi') {
                $dashboardRoute = route('teknisi.dashboard');
            } elseif (in_array($roleName, ['teknisi motor', 'teknisi mobil'])) {
                $dashboardRoute = route('teknisi.servis.dashboard');
            } elseif ($roleName === 'kasir') {
                $dashboardRoute = route('kasir.dashboard');
            } elseif ($roleName === 'kasirfutsal') {
                $dashboardRoute = route('kasirfutsal.dashboard');
            } elseif ($roleName === 'pelanggan') {
                // Pelanggan tidak seharusnya login via internal page
                Auth::logout();
                return redirect()->route('home')->with('error', 'Akses ditolak.');
            }

            // Catat log login
            $this->function_log('Auth', 'login', 'User ' . Auth::user()->name . ' login');

            return redirect($dashboardRoute);
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
        return redirect()->route('staff.login');
    }

    // ── REGISTER ──────────────────────────────────────────

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect('/');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'username'     => 'required|string|unique:users,username',
            'no_hp'        => 'required|string|max:20',
            'email'        => 'required|email|unique:users,email',
            'password'     => 'required|min:6|confirmed',
        ]);

        // Ambil nama panggilan dari kata pertama
        $name = explode(' ', trim($request->nama_lengkap))[0];

        $user = User::create([
            'name'         => $name,
            'nama_lengkap' => $request->nama_lengkap,
            'username'     => $request->username,
            'no_hp'        => $request->no_hp,
            'email'        => $request->email,
            'password'     => Hash::make($request->password),
        ]);

        // ambil role pelanggan
        $role = DB::table('roles')->where('nama', 'pelanggan')->first();

        // insert ke pivot
        DB::table('roles_users')->insert([
            'user_id' => $user->id,
            'role_id' => $role->id,
        ]);

        Auth::login($user);

        return redirect()->route('home');
    }
}
