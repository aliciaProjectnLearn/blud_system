<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        // Ambil daftar nama role user
        $userRoles = $user->roles->pluck('nama')->toArray();

        // Cek apakah user memiliki salah satu role yang diizinkan
        foreach ($roles as $role) {
            if (in_array($role, $userRoles)) {
                return $next($request);
            }
        }

        abort(403, 'Unauthorized - Anda tidak memiliki akses ke halaman ini.');
    }
}