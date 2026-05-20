<?php

namespace App\Http\Controllers\AdminAc;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Traits\Loggable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class TeknisiController extends Controller
{
    use Loggable;

    public function index(Request $request)
    {
        $search       = $request->search;
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

        if ($filterStatus) {
            $teknisis->setCollection(
                $teknisis->getCollection()->filter(function ($user) use ($filterStatus) {
                    return $user->status_dinamis === $filterStatus;
                })->values()
            );
        }

        $today       = \Carbon\Carbon::today();
        $filterBulan = $request->get('bulan_filter', $today->month);

        $dataTeknisi = User::whereHas('roles', function ($q) {
                $q->where('nama', 'Teknisi');
            })
            ->withCount(['pekerjaanTeknisi as total_selesai_bulan_ini' => function ($q) use ($filterBulan, $today) {
                $q->where('status', 'selesai')
                  ->whereMonth('updated_at', $filterBulan)
                  ->whereYear('updated_at', $today->year);
            }])
            ->withCount(['pekerjaanTeknisi as total_aktif' => function ($q) {
                $q->where('status', 'proses');
            }])
            ->get();

        return view('adminac.teknisi.index', compact('teknisis', 'search', 'filterStatus', 'dataTeknisi', 'filterBulan'));
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
            'no_hp'        => 'required|string|max:20|unique:users,no_hp',
            'email'        => 'required|email|unique:users,email',
            'password'     => 'required|string|min:6',
        ], [
            'username.unique' => 'Username sudah digunakan.',
            'email.unique'    => 'Email sudah terdaftar.',
            'no_hp.unique'    => 'No. HP sudah terdaftar.',
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

        $this->function_log('AC', 'create', 'Admin AC menambahkan teknisi baru: ' . $request->name);

        return redirect()->route('admin.ac.teknisi.index')
            ->with('success', 'Data teknisi berhasil diperbarui.');
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
            'username'     => ['required', 'string', 'max:255', \Illuminate\Validation\Rule::unique('users', 'username')->ignore($teknisi->id)],
            'no_hp'        => ['required', 'string', 'max:20', \Illuminate\Validation\Rule::unique('users', 'no_hp')->ignore($teknisi->id)],
            'email'        => ['required', 'email', \Illuminate\Validation\Rule::unique('users', 'email')->ignore($teknisi->id)],
            'password'     => 'nullable|string|min:6',
        ], [
            'username.unique' => 'Username sudah digunakan.',
            'email.unique'    => 'Email sudah terdaftar.',
            'no_hp.unique'    => 'No. HP sudah terdaftar.',
            'password.min'    => 'Password minimal 6 karakter.',
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

        $this->function_log('AC', 'update', 'Admin AC mengubah data teknisi: ' . $teknisi->name);

        return redirect()->route('admin.ac.teknisi.index')
            ->with('success', 'Data teknisi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $teknisi = User::whereHas('roles', function ($q) {
            $q->where('nama', 'Teknisi');
        })->findOrFail($id);

        $adaBookingAktif = DB::table('booking_ac')
            ->where('teknisi_id', $teknisi->id)
            ->where('status', 'proses')
            ->exists();

        if ($adaBookingAktif) {
            return redirect()->route('admin.ac.teknisi.index')
                ->with('error', 'Teknisi tidak dapat dihapus karena sedang menangani booking.');
        }

        $namaTeknisi = $teknisi->name;

        $teknisi->roles()->detach();
        $teknisi->delete();

        $this->function_log('AC', 'delete', 'Admin AC menghapus teknisi: ' . $namaTeknisi);


        return redirect()->route('admin.ac.teknisi.index')
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