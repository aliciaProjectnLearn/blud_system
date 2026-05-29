<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Traits\Loggable;
use Throwable;

class UserController extends Controller
{
    use Loggable;

    public function index()
    {
        $search = request('search');

        $users = User::with('roles')
            ->whereHas('roles', function ($query) {
                $query->where('nama', 'not like', 'pelanggan')
                      ->where('nama', 'not like', 'Pelanggan')
                      ->where('nama', 'not like', 'user')
                      ->where('nama', 'not like', 'User');
            })
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%$search%");
            })
            ->paginate(10);

        return view('dashboard.users.index', compact('users', 'search'));
    }

    public function indexPelanggan()
    {
        $search = request('search');

        $users = User::with('roles')
            ->whereHas('roles', function ($query) {
                $query->where('nama', 'like', 'Pelanggan%');
            })
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%$search%");
            })
            ->paginate(10);

        return view('dashboard.users.pelanggan', compact('users', 'search'));
    }

    public function create(Request $request)
    {
        $roles = Role::whereNotIn('nama', ['pelanggan', 'Pelanggan', 'user', 'User'])
            ->orderBy('nama')
            ->get();

        // Pre-fill role jika datang dari CMS layanan
        $preSelectedRoleId   = $request->query('role_id');
        $preSelectedRoleNama = $request->query('role_nama');

        return view('dashboard.users.create', compact(
            'roles', 
            'preSelectedRoleId', 
            'preSelectedRoleNama'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'username'     => 'required|string|max:255|unique:users,username',
            'nama_lengkap' => 'required|string|max:255',
            'no_hp'        => 'required|string|max:20|unique:users,no_hp',
            'email'        => 'required|email|unique:users,email',
            'password'     => 'required|min:6|confirmed',
            'role'         => 'required|exists:roles,id',
        ], [
            'username.unique' => 'Username sudah digunakan.',
            'email.unique'    => 'Email sudah terdaftar.',
            'no_hp.unique'    => 'No. HP sudah terdaftar.',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $user = User::create([
                    'name'         => $request->nama_lengkap,
                    'username'     => $request->username,
                    'nama_lengkap' => $request->nama_lengkap,
                    'no_hp'        => $request->no_hp,
                    'email'        => $request->email,
                    'password'     => Hash::make($request->password),
                ]);

                $user->roles()->attach($request->role);
            });

            // Auto-generate files for the dynamic admin if missing
            $role = Role::find($request->role);
            if ($role && \Illuminate\Support\Str::startsWith($role->nama, 'Admin') && !in_array(strtolower($role->nama), ['superadmin', 'adminfutsal', 'adminkantin', 'adminac', 'adminservis'])) {
                $serviceStudly = substr($role->nama, 5);
                $serviceSlug = \Illuminate\Support\Str::kebab($serviceStudly);
                
                $layanan = \App\Models\Layanan::where('route_name', "user.{$serviceSlug}.index")->first();
                $namaLayanan = $layanan ? $layanan->nama_layanan : \Illuminate\Support\Str::title(str_replace('-', ' ', $serviceSlug));
                
                $adminControllerDir  = app_path("Http/Controllers/Admin{$serviceStudly}");
                $adminControllerPath = "{$adminControllerDir}/DashboardController.php";
                
                if (!is_dir($adminControllerDir)) {
                    \Illuminate\Support\Facades\File::makeDirectory($adminControllerDir, 0755, true);
                }
                if (!file_exists($adminControllerPath)) {
                    $adminControllerContent = '<?php' . PHP_EOL . PHP_EOL
                        . "namespace App\Http\Controllers\Admin{$serviceStudly};" . PHP_EOL . PHP_EOL
                        . 'use App\Http\Controllers\Controller;' . PHP_EOL
                        . 'use Illuminate\Http\Request;' . PHP_EOL . PHP_EOL
                        . 'class DashboardController extends Controller' . PHP_EOL
                        . '{' . PHP_EOL
                        . '    public function index()' . PHP_EOL
                        . '    {' . PHP_EOL
                        . "        return view('admin{$serviceSlug}.index');" . PHP_EOL
                        . '    }' . PHP_EOL
                        . '}' . PHP_EOL;
                    \Illuminate\Support\Facades\File::put($adminControllerPath, $adminControllerContent);
                }
                
                $adminViewDir  = resource_path("views/admin{$serviceSlug}");
                $adminViewPath = "{$adminViewDir}/index.blade.php";
                if (!is_dir($adminViewDir)) {
                    \Illuminate\Support\Facades\File::makeDirectory($adminViewDir, 0755, true);
                }
                if (!file_exists($adminViewPath)) {
                    $adminViewContent = "@extends('layouts.app')" . PHP_EOL . PHP_EOL
                        . "@section('title', 'Dashboard {$namaLayanan}')" . PHP_EOL . PHP_EOL
                        . "@section('content')" . PHP_EOL
                        . '<div class="d-sm-flex align-items-center justify-content-between mb-4">' . PHP_EOL
                        . "    <h1 class=\"h3 mb-0 text-gray-800\">Dashboard {$namaLayanan}</h1>" . PHP_EOL
                        . '</div>' . PHP_EOL
                        . '<div class="alert alert-info">' . PHP_EOL
                        . '    Modul ini belum dikonfigurasi. Silakan hubungi developer.' . PHP_EOL
                        . '</div>' . PHP_EOL
                        . '@endsection' . PHP_EOL;
                    \Illuminate\Support\Facades\File::put($adminViewPath, $adminViewContent);
                }

                // Check routes in web.php
                $webPhpPath = base_path('routes/web.php');
                if (file_exists($webPhpPath)) {
                    $webContent = \Illuminate\Support\Facades\File::get($webPhpPath);
                    if (!str_contains($webContent, "admin/{$serviceSlug}")) {
                        $routeBlock = PHP_EOL . PHP_EOL
                            . "// ===== AUTO-GENERATED: {$namaLayanan} =====" . PHP_EOL
                            . '// User Route' . PHP_EOL
                            . "Route::prefix('{$serviceSlug}')->name('user.{$serviceSlug}.')" . PHP_EOL
                            . "    ->group(function () {" . PHP_EOL
                            . "    Route::get('/', [App\\Http\\Controllers\\User\\{$serviceStudly}Controller::class, 'index'])" . PHP_EOL
                            . "        ->name('index');" . PHP_EOL
                            . '});' . PHP_EOL . PHP_EOL
                            . '// Admin Route' . PHP_EOL
                            . "Route::middleware(['auth', 'role:Superadmin'])->prefix('admin/{$serviceSlug}')->name('admin.{$serviceSlug}.')" . PHP_EOL
                            . "    ->group(function () {" . PHP_EOL
                            . "    Route::get('/dashboard', [App\\Http\\Controllers\\Admin{$serviceStudly}\\DashboardController::class, 'index'])" . PHP_EOL
                            . "        ->name('dashboard');" . PHP_EOL
                            . '});' . PHP_EOL
                            . "// ===== END AUTO-GENERATED: {$namaLayanan} =====" . PHP_EOL;
                        \Illuminate\Support\Facades\File::append($webPhpPath, $routeBlock);
                    }
                }
            }

            $this->function_log('User', 'create', 'Super Admin menambahkan user baru: ' . $request->nama_lengkap);

        } catch (Throwable $e) {
            return back()->withInput()->with('error', 'Gagal menyimpan user: ' . $e->getMessage());
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $user  = User::with('roles')->findOrFail($id);
        $roles = Role::orderBy('nama')->get();

        return view('dashboard.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'username'     => 'required|string|max:255|unique:users,username,' . $id,
            'nama_lengkap' => 'required|string|max:255',
            'no_hp'        => 'required|string|max:20|unique:users,no_hp,' . $id,
            'email'        => 'required|email|unique:users,email,' . $id,
            'password'     => 'nullable|min:6|confirmed',
            'role'         => 'required|exists:roles,id',
        ], [
            'username.unique' => 'Username sudah digunakan akun lain.',
            'email.unique'    => 'Email sudah digunakan akun lain.',
            'no_hp.unique'    => 'No. HP sudah digunakan akun lain.',
        ]);

        try {
            DB::transaction(function () use ($request, $user) {
                $data = [
                    'name'         => $request->nama_lengkap,
                    'username'     => $request->username,
                    'nama_lengkap' => $request->nama_lengkap,
                    'no_hp'        => $request->no_hp,
                    'email'        => $request->email,
                ];

                if ($request->filled('password')) {
                    $data['password'] = Hash::make($request->password);
                }

                $user->update($data);
                $user->roles()->sync([$request->role]);
            });

            $this->function_log('User', 'update', 'Super Admin mengubah data user: ' . $user->name);

        } catch (Throwable $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui user: ' . $e->getMessage());
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = User::with('roles')->findOrFail($id);

        $isPelanggan = $user->roles->contains(fn($r) =>
            in_array(strtolower($r->nama), ['pelanggan', 'user'])
        );

        $namaUser = $user->name;

        try {
            DB::transaction(function () use ($user) {
                $user->roles()->detach();
                $user->delete();
            });

            $this->function_log('User', 'delete', 'Menghapus user ' . $namaUser);

        } catch (\Throwable $e) {
            $pesan = 'Gagal menghapus user karena masih memiliki data terkait (booking/transaksi).';
            if (str_contains($e->getMessage(), 'foreign key') || str_contains($e->getMessage(), 'Integrity constraint')) {
                $pesan = 'Tidak dapat menghapus akun ini karena masih memiliki riwayat booking atau transaksi aktif.';
            }
            return back()->with('error', $pesan);
        }

        $route = $isPelanggan ? 'admin.users.pelanggan' : 'admin.users.index';

        return redirect()->route($route)
            ->with('success', "Akun {$namaUser} berhasil dihapus.");
    }

    public function show($id)
    {
        $user = User::with('roles')->findOrFail($id);

        return view('dashboard.users.show', compact('user'));
    }
}