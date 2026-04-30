<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Traits\Loggable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // proses login
        $request->authenticate();

        // regenerate session
        $request->session()->regenerate();

        // ambil user yang login
        $user = Auth::user();

        // catat log login
        Loggable::log(
            'Auth',
            'login',
            'User ' . $user->email . ' berhasil login'
        );

        // Redirect berdasarkan role
        $roleName = strtolower($user->roles->first()->nama ?? '');
        
        $dashboardRoute = match($roleName) {
            'superadmin'   => route('dashboard'),
            'adminfutsal'  => route('adminfutsal.dashboard'),
            'adminkantin'  => route('adminkantin.dashboard'),
            'adminac'      => route('adminac.dashboard'),
            'adminservis'  => route('adminservis.dashboard'),
            'kasirservis'  => route('kasir.dashboard'),
            'kasirfutsal'  => route('kasirfutsal.dashboard'),
            'teknisiac'    => route('teknisi.dashboard'),
            'teknisiservis' => route('teknisi.dashboard'),
            default        => route('gateway'),
        };

        return redirect()->intended($dashboardRoute);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // ambil user sebelum logout
        $user = Auth::user();

        // catat log logout
        Loggable::log(
            'Auth',
            'logout',
            'User ' . ($user->email ?? 'unknown') . ' logout'
        );

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}