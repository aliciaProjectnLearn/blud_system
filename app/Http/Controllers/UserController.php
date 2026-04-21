<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        $search = request('search');

        $users = User::with('roles')
            ->whereHas('roles', function ($query) {
                $query->where('nama', 'like', 'Admin%');
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
        // Filter hanya role yang diinginkan
        $roles = DB::table('roles')
            ->whereIn('nama', ['Adminfutsal', 'Adminkantin', 'Adminac'])
            ->get();

        return view('dashboard.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'username'    => 'required|string|max:255|unique:users,username',
            'nama_lengkap' => 'required|string|max:255',
            'no_hp'       => 'required|string|max:20',
            'email'       => 'required|email|unique:users,email',
            'password'    => 'required|min:6|confirmed',
            'role'        => 'required|exists:roles,id',
        ]);

        $user = User::create([
            'name'         => $request->nama_lengkap,
            'username'     => $request->username,
            'nama_lengkap' => $request->nama_lengkap,
            'no_hp'        => $request->no_hp,
            'email'        => $request->email,
            'password'     => Hash::make($request->password),
        ]);

        // assign role via tabel roles_users
        $user->roles()->attach($request->role);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil ditambahkan');
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
            'no_hp'        => 'required|string|max:20',
            'email'        => 'required|email|unique:users,email,' . $id,
            'password'     => 'nullable|min:6|confirmed',
            'role'         => 'required|exists:roles,id',
        ]);

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

        // sync role via tabel roles_users
        $user->roles()->sync([$request->role]);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil diupdate');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User berhasil dihapus');
    }

    public function show($id)
    {
        $user = User::with('roles')->findOrFail($id);
        return view('dashboard.users.show', compact('user'));
    }
}
