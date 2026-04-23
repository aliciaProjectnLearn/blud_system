<?php

namespace App\Http\Controllers\AdminServis;

use App\Http\Controllers\Controller;
use App\Models\BookingServis;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class TeknisiController extends Controller
{
    /**
     * Tampilkan daftar teknisi servis (Teknisi Motor & Teknisi Mobil).
     */
    public function index(Request $request)
    {
        $roleNames = ['Teknisi Motor', 'Teknisi Mobil'];

        $query = User::whereHas('roles', fn($q) => $q->whereIn('nama', $roleNames))
            ->with('roles');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%");
            });
        }

        // Filter role/tipe
        if ($request->filled('tipe')) {
            $query->whereHas('roles', fn($q) => $q->where('nama', $request->tipe));
        }

        // Filter status aktif
        if ($request->filled('status')) {
            if ($request->status === 'aktif') {
                $query->where('status_aktif', true);
            } elseif ($request->status === 'nonaktif') {
                $query->where('status_aktif', false);
            }
        }

        $teknisis = $query->latest()->paginate(10)->withQueryString();

        // Tambahkan statistik per teknisi
        $bulanFilter = $request->input('bulan_filter', now()->month);
        $teknisis->getCollection()->transform(function ($teknisi) use ($bulanFilter) {
            $teknisi->total_selesai_bulan_ini = BookingServis::where('teknisi_id', $teknisi->id)
                ->where('status', 'selesai')
                ->whereMonth('tanggal_booking', $bulanFilter)
                ->whereYear('tanggal_booking', now()->year)
                ->count();

            $teknisi->total_aktif = BookingServis::where('teknisi_id', $teknisi->id)
                ->whereIn('status', ['dikonfirmasi', 'diproses'])
                ->count();

            return $teknisi;
        });

        $roles = Role::whereIn('nama', $roleNames)->get();

        return view('adminservis.teknisi.index', compact('teknisis', 'roles', 'bulanFilter'));
    }

    /**
     * Form tambah teknisi.
     */
    public function create()
    {
        $roles = Role::whereIn('nama', ['Teknisi Motor', 'Teknisi Mobil'])->get();
        return view('adminservis.teknisi.create', compact('roles'));
    }

    /**
     * Simpan teknisi baru (buat user + assign role).
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'name'         => 'required|string|max:255',
            'username'     => 'required|string|max:255|unique:users,username',
            'email'        => 'required|email|unique:users,email',
            'no_hp'        => 'required|string|max:20',
            'password'     => 'required|string|min:8|confirmed',
            'role_id'      => ['required', Rule::exists('roles', 'id')->where(function ($query) {
                $query->whereIn('nama', ['Teknisi Motor', 'Teknisi Mobil']);
            })],
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'name.required'         => 'Nama wajib diisi.',
            'username.required'     => 'Username wajib diisi.',
            'username.unique'       => 'Username sudah digunakan.',
            'email.required'        => 'Email wajib diisi.',
            'email.unique'          => 'Email sudah terdaftar.',
            'no_hp.required'        => 'No. HP wajib diisi.',
            'password.required'     => 'Password wajib diisi.',
            'password.min'          => 'Password minimal 8 karakter.',
            'password.confirmed'    => 'Konfirmasi password tidak cocok.',
            'role_id.required'      => 'Tipe teknisi wajib dipilih.',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'nama_lengkap' => $request->nama_lengkap,
                'name'         => $request->name,
                'username'     => $request->username,
                'email'        => $request->email,
                'no_hp'        => $request->no_hp,
                'password'     => Hash::make($request->password),
                'status_aktif' => true,
            ]);

            $user->roles()->attach($request->role_id);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menambahkan teknisi: ' . $e->getMessage());
        }

        return redirect()->route('adminservis.teknisi.index')
            ->with('success', 'Teknisi berhasil ditambahkan.');
    }

    /**
     * Detail teknisi + riwayat pekerjaan.
     */
    public function show(Request $request, $id)
    {
        $teknisi = User::whereHas('roles', fn($q) => $q->whereIn('nama', ['Teknisi Motor', 'Teknisi Mobil']))
            ->with('roles')
            ->findOrFail($id);

        $query = BookingServis::where('teknisi_id', $id)
            ->with(['user', 'layananServis', 'pembayaranServis']);

        if ($request->filled('status_riwayat')) {
            $query->where('status', $request->status_riwayat);
        }

        $riwayatPekerjaan = $query->latest()->paginate(10)->withQueryString();

        $statistik = [
            'total_selesai'  => BookingServis::where('teknisi_id', $id)->where('status', 'selesai')->count(),
            'total_aktif'    => BookingServis::where('teknisi_id', $id)->whereIn('status', ['dikonfirmasi', 'diproses'])->count(),
            'total_all'      => BookingServis::where('teknisi_id', $id)->count(),
            'bulan_ini'      => BookingServis::where('teknisi_id', $id)
                ->where('status', 'selesai')
                ->whereMonth('tanggal_booking', now()->month)
                ->whereYear('tanggal_booking', now()->year)
                ->count(),
        ];

        return view('adminservis.teknisi.show', compact('teknisi', 'riwayatPekerjaan', 'statistik'));
    }

    /**
     * Form edit teknisi.
     */
    public function edit($id)
    {
        $teknisi = User::whereHas('roles', fn($q) => $q->whereIn('nama', ['Teknisi Motor', 'Teknisi Mobil']))
            ->with('roles')
            ->findOrFail($id);

        $roles = Role::whereIn('nama', ['Teknisi Motor', 'Teknisi Mobil'])->get();

        return view('adminservis.teknisi.edit', compact('teknisi', 'roles'));
    }

    /**
     * Update data teknisi.
     */
    public function update(Request $request, $id)
    {
        $teknisi = User::whereHas('roles', fn($q) => $q->whereIn('nama', ['Teknisi Motor', 'Teknisi Mobil']))
            ->findOrFail($id);

        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'name'         => 'required|string|max:255',
            'username'     => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($teknisi->id)],
            'email'        => ['required', 'email', Rule::unique('users', 'email')->ignore($teknisi->id)],
            'no_hp'        => 'required|string|max:20',
            'password'     => 'nullable|string|min:8|confirmed',
            'role_id'      => ['required', Rule::exists('roles', 'id')->where(function ($query) {
                $query->whereIn('nama', ['Teknisi Motor', 'Teknisi Mobil']);
            })],
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'email.unique'          => 'Email sudah digunakan akun lain.',
            'username.unique'       => 'Username sudah digunakan akun lain.',
            'password.min'          => 'Password minimal 8 karakter.',
            'password.confirmed'    => 'Konfirmasi password tidak cocok.',
            'role_id.required'      => 'Tipe teknisi wajib dipilih.',
        ]);

        DB::beginTransaction();
        try {
            $data = [
                'nama_lengkap' => $request->nama_lengkap,
                'name'         => $request->name,
                'username'     => $request->username,
                'email'        => $request->email,
                'no_hp'        => $request->no_hp,
            ];

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $teknisi->update($data);

            // Sync role (hanya boleh 1 dari 2 role teknisi)
            $oldRoles = $teknisi->roles()->whereIn('nama', ['Teknisi Motor', 'Teknisi Mobil'])->pluck('roles.id');
            $teknisi->roles()->detach($oldRoles);
            $teknisi->roles()->attach($request->role_id);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }

        return redirect()->route('adminservis.teknisi.index')
            ->with('success', 'Data teknisi berhasil diperbarui.');
    }

    /**
     * Toggle status aktif/nonaktif teknisi.
     */
    public function toggleStatus($id)
    {
        $teknisi = User::whereHas('roles', fn($q) => $q->whereIn('nama', ['Teknisi Motor', 'Teknisi Mobil']))
            ->findOrFail($id);

        // Cek apakah sedang ada booking aktif
        $adaBookingAktif = BookingServis::where('teknisi_id', $id)
            ->whereIn('status', ['dikonfirmasi', 'diproses'])
            ->exists();

        if ($adaBookingAktif && $teknisi->status_aktif) {
            return back()->with('error', 'Teknisi tidak dapat dinonaktifkan karena masih memiliki booking yang sedang berjalan.');
        }

        $teknisi->update(['status_aktif' => !$teknisi->status_aktif]);

        $pesan = $teknisi->status_aktif ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Teknisi berhasil {$pesan}.");
    }

    /**
     * Hapus teknisi (validasi: tidak boleh ada booking aktif).
     */
    public function destroy($id)
    {
        $teknisi = User::whereHas('roles', fn($q) => $q->whereIn('nama', ['Teknisi Motor', 'Teknisi Mobil']))
            ->findOrFail($id);

        $adaBookingAktif = BookingServis::where('teknisi_id', $id)
            ->whereIn('status', ['dikonfirmasi', 'diproses'])
            ->exists();

        if ($adaBookingAktif) {
            return back()->with('error', 'Teknisi tidak dapat dihapus karena masih memiliki booking yang sedang berjalan.');
        }

        DB::beginTransaction();
        try {
            $teknisi->roles()->detach();
            $teknisi->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus teknisi: ' . $e->getMessage());
        }

        return redirect()->route('adminservis.teknisi.index')
            ->with('success', 'Teknisi berhasil dihapus.');
    }
}
