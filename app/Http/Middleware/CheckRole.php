<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): mixed
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // ✅ Refresh user dari DB agar tidak pakai cache object lama
        $user = $user->fresh();

        $allowedRoles = explode('|', $role);

        // Fetch all roles of the user
        $userRoles = DB::table('roles_users')
            ->join('roles', 'roles.id', '=', 'roles_users.role_id')
            ->where('roles_users.user_id', $user->id)
            ->pluck('roles.nama')
            ->toArray();

        // 1. Check exact match (case-insensitive)
        $hasExactRole = collect($userRoles)->contains(function($userRole) use ($allowedRoles) {
            return collect($allowedRoles)->contains(function($allowed) use ($userRole) {
                return strtolower($userRole) === strtolower($allowed);
            });
        });

        if ($hasExactRole) {
            return $next($request);
        }

        // 2. Handle dynamic admin roles (e.g. AdminKolamRenang)
        // Check if user has any role starting with 'Admin'
        $hasAnyAdminRole = collect($userRoles)->contains(function($userRole) {
            return Str::startsWith($userRole, 'Admin');
        });

        if ($hasAnyAdminRole) {
            // Check if the current route is a general admin route.
            // A general admin route is defined by having multiple default admin roles in the whitelist.
            $isGeneralAdminRoute = count($allowedRoles) > 1 && collect($allowedRoles)->contains(function($allowed) {
                return in_array(strtolower($allowed), ['adminfutsal', 'adminkantin', 'adminac', 'adminservis']);
            });

            if ($isGeneralAdminRoute) {
                return $next($request);
            }

            // Check if this is the dynamic admin's own system route.
            // Dynamic system routes are defined with role:Superadmin, but have a path starting with admin/{slug}
            // where Admin{Studly(slug)} matches the user's role.
            foreach ($userRoles as $userRole) {
                if (Str::startsWith($userRole, 'Admin') && !in_array(strtolower($userRole), ['superadmin', 'adminfutsal', 'adminkantin', 'adminac', 'adminservis'])) {
                    // Extract the service name from role (e.g., 'AdminKolamRenang' -> 'KolamRenang')
                    $serviceStudly = substr($userRole, 5); // 'KolamRenang'
                    $serviceSlug = Str::kebab($serviceStudly); // 'kolam-renang'
                    
                    if ($request->is("admin/{$serviceSlug}") || $request->is("admin/{$serviceSlug}/*")) {
                        return $next($request);
                    }
                }
            }
        }

        abort(403, 'Akses ditolak.');
    }
}
