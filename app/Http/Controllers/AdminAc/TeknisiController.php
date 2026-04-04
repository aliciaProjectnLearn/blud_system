<?php

namespace App\Http\Controllers\AdminAc;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class TeknisiController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $filterStatus = $request->status;

        $teknisis = User::whereHas('roles', function ($q) {
            $q->where('nama', 'Teknisi');
        })
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%")
                  ->orWhere('nama_lengkap', 'like', "%{$search}%");
            });
        })
        ->paginate(10)
        ->through(function ($user) {
            $isSibuk = DB::table('booking_ac')
                ->where('teknisi_id', $user->id)
                ->where('status', 'proses')
                ->exists();

            $user->status_dinamis = $isSibuk ? 'sibuk' : 'tersedia';
            return $user;
        });

        // Filter status dilakukan setelah paginate via collection
        if ($filterStatus) {
            $teknisis->setCollection(
                $teknisis->getCollection()->filter(function ($user) use ($filterStatus) {
                    return $user->status_dinamis === $filterStatus;
                })->values()
            );
        }

        return view('adminac.teknisi.index', compact('teknisis', 'search', 'filterStatus'));
    }

    public function create()
    {
        return view('adminac.teknisi.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'nama_lengkap' => 'required|string|max:255',
            'username'     => 'required|string|max:255|unique:users,username',
            'no_hp'        => 'required|string|max:20',
            'email'        => 'required|email|unique:users,email',
            'password'     => 'required|string|min:6',
        ]);

        $user = User::create([
            'name'         => $request->name,
            'nama_lengkap' => $request->nama_lengkap,
            'username'     => $request->username,
            'no_hp'        => $request->no_hp,
            'email'        => $request->email,
            'password'     => Hash::make($request->password),
        ]);

        $role = Role::where('nama', 'Teknisi')->first();
        if ($role) {
            $user->roles()->attach($role->id);
        }

        return redirect()->route('adminac.teknisi.index')
            ->with('success', 'Teknisi berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $teknisi = User::whereHas('roles', function ($q) {
            $q->where('nama', 'Teknisi');
        })->findOrFail($id);

        return view('adminac.teknisi.edit', compact('teknisi'));
    }

    public function update(Request $request, $id)
    {
        $teknisi = User::whereHas('roles', function ($q) {
            $q->where('nama', 'Teknisi');
        })->findOrFail($id);

        $request->validate([
            'name'         => 'required|string|max:255',
            'nama_lengkap' => 'required|string|max:255',
            'username'     => 'required|string|max:255|unique:users,username,' . $id,
            'no_hp'        => 'required|string|max:20',
            'email'        => 'required|email|unique:users,email,' . $id,
            'password'     => 'nullable|string|min:6',
        ]);

        $data = [
            'name'         => $request->name,
            'nama_lengkap' => $request->nama_lengkap,
            'username'     => $request->username,
            'no_hp'        => $request->no_hp,
            'email'        => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $teknisi->update($data);

        return redirect()->route('adminac.teknisi.index')
            ->with('success', 'Data teknisi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $teknisi = User::whereHas('roles', function ($q) {
            $q->where('nama', 'Teknisi');
        })->findOrFail($id);

        // Cek apakah teknisi sedang punya booking aktif
        $adaBookingAktif = DB::table('booking_ac')
            ->where('teknisi_id', $teknisi->id)
            ->where('status', 'proses')
            ->exists();

        if ($adaBookingAktif) {
            return redirect()->route('adminac.teknisi.index')
                ->with('error', 'Teknisi tidak dapat dihapus karena sedang menangani booking.');
        }

        $teknisi->roles()->detach();
        $teknisi->delete();

        return redirect()->route('adminac.teknisi.index')
            ->with('success', 'Teknisi berhasil dihapus.');
    }

    public function cekKetersediaan($id)
    {
        $teknisi = User::findOrFail($id);

        $isSibuk = DB::table('booking_ac')
            ->where('teknisi_id', $teknisi->id)
            ->where('status', 'proses')
            ->exists();

        return response()->json([
            'id'     => $teknisi->id,
            'name'   => $teknisi->name,
            'status' => $isSibuk ? 'sibuk' : 'tersedia',
        ]);
    }
}
