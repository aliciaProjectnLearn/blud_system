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
            \Log::info('CheckRole: no user, redirect login');
            return redirect()->route('login');
        }

        $allowedRoles = explode('|', $role);

        $hasRole = DB::table('roles_users')
            ->join('roles', 'roles.id', '=', 'roles_users.role_id')
            ->where('roles_users.user_id', $user->id)
            ->whereIn('roles.nama', $allowedRoles)
            ->exists();

        \Log::info('CheckRole debug', [
            'user_id'      => $user->id,
            'email'        => $user->email,
            'allowedRoles' => $allowedRoles,
            'hasRole'      => $hasRole,
            'url'          => $request->url(),
        ]);

        if (!$hasRole) {
            abort(403, 'Akses ditolak.');
        }

        return $next($request);
    }
}
