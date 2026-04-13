<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): mixed
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Support multiple roles dipisah pipe: role:Superadmin|Adminfutsal|Adminkantin
        $allowedRoles = explode('|', $role);

        $hasRole = DB::table('roles_users')
            ->join('roles', 'roles.id', '=', 'roles_users.role_id')
            ->where('roles_users.user_id', $user->id)
            ->whereIn('roles.nama', $allowedRoles)
            ->exists();

        if (!$hasRole) {
            abort(403, 'Akses ditolak.');
        }

        return $next($request);
    }
}
