<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Throwable;

class UserController extends Controller
{
    public function index()
    {
        $search = request('search');

        $users = User::with('roles')
            ->whereHas('roles', function ($query) {
                // Tampilkan semua role staff kecuali pelanggan/user biasa
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

    public function create()
    {
        // Ambil semua role staff dari database (kecuali role pelanggan/user biasa)
        $roles = Role::whereNotIn('nama', ['pelanggan', 'Pelanggan', 'user', 'User'])
            ->orderBy('nama')
            ->get();

        return view('dashboard.users.create', compact('roles'));
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

                // Assign role via tabel pivot roles_users
                $user->roles()->attach($request->role);
            });
        } catch (Throwable $e) {
            return back()->withInput()->with('error', 'Gagal menyimpan user: ' . $e->getMessage());
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $user = User::with('roles')->findOrFail($id);
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

                // Sync role via tabel pivot roles_users (replace semua role lama)
                $user->roles()->sync([$request->role]);
            });
        } catch (Throwable $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui user: ' . $e->getMessage());
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = User::with('roles')->findOrFail($id);

        // Deteksi apakah user ini pelanggan untuk redirect yang tepat
        $isPelanggan = $user->roles->contains(fn($r) =>
            in_array(strtolower($r->nama), ['pelanggan', 'user'])
        );

        try {
            DB::transaction(function () use ($user) {
                // Lepas semua relasi role terlebih dahulu
                $user->roles()->detach();
                // Hapus user
                $user->delete();
            });
        } catch (\Throwable $e) {
            $pesan = 'Gagal menghapus user karena masih memiliki data terkait (booking/transaksi).';
            // Jika foreign key constraint, beri pesan spesifik
            if (str_contains($e->getMessage(), 'foreign key') || str_contains($e->getMessage(), 'Integrity constraint')) {
                $pesan = 'Tidak dapat menghapus akun ini karena masih memiliki riwayat booking atau transaksi aktif.';
            }
            return back()->with('error', $pesan);
        }

        $route    = $isPelanggan ? 'admin.users.pelanggan' : 'admin.users.index';
        $namaUser = $user->name;

        return redirect()->route($route)
            ->with('success', "Akun \"{$namaUser}\" berhasil dihapus.");
    }

    public function show($id)
    {
        $user = User::with('roles')->findOrFail($id);
        return view('dashboard.users.show', compact('user'));
    }
}
