<?php

namespace App\Http\Controllers\AdminKantin;

use App\Http\Controllers\Controller;
use App\Models\Penyewa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PenyewaController extends Controller
{
    public function index(Request $request)
    {
        // Fitur pencarian sederhana berdasarkan nama pengguna atau nama usaha
        $search = $request->query('search');

        $penyewas = Penyewa::with(['user', 'sewaRuko.ruko'])
            ->when($search, function ($query, $search) {
                $query->where('nama_usaha', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('nama_lengkap', 'like', "%{$search}%");
                    });
            })
            ->latest()
            ->paginate(10);

        return view('adminkantin.penyewa.index', compact('penyewas', 'search'));
    }

    public function create()
    {
        return view('adminkantin.penyewa.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'no_hp' => 'required|string|max:20',
            'nama_usaha' => 'required|string|max:255',
            'alamat' => 'required|string',
            'nik' => 'required|string|max:20|unique:users,nik',
        ]);

        DB::beginTransaction();
        try {
            // Buat dummy email karena di tabel users email bersifat unik
            $dummyEmail = 'penyewa_' . time() . '@blud.com';

            $user = User::create([
                'name' => $request->nama_lengkap,
                'username' => 'penyewa_' . time() . '_' . mt_rand(10, 99),
                'email' => $dummyEmail,
                'password' => bcrypt(uniqid()), // default random password
                'nama_lengkap' => $request->nama_lengkap,
                'no_hp' => $request->no_hp,
                'nik' => $request->nik,
                'status_futsal' => 'active',
            ]);

            Penyewa::create([
                'user_id' => $user->id,
                'nama_usaha' => $request->nama_usaha,
                'alamat' => $request->alamat,
            ]);

            DB::commit();
            return redirect()
                ->route('adminkantin.penyewa.index')
                ->with('success', 'Data penyewa berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->with('error', 'Gagal menambahkan penyewa: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Penyewa $penyewa)
    {
        // Load relasi User dan sewaRuko diurutkan dari yang terbaru
        $penyewa->load([
            'user',
            'sewaRuko' => function ($query) {
                $query->orderBy('tgl_mulai', 'desc')->with('ruko');
            }
        ]);

        return view('adminkantin.penyewa.show', compact('penyewa'));
    }

    public function edit(Penyewa $penyewa)
    {
        $penyewa->load('user');
        return view('adminkantin.penyewa.edit', compact('penyewa'));
    }

    public function update(Request $request, Penyewa $penyewa)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'no_hp' => 'required|string|max:20',
            'nama_usaha' => 'required|string|max:255',
            'alamat' => 'required|string',
            'nik' => ['required', 'string', 'max:20', Rule::unique('users', 'nik')->ignore($penyewa->user_id)],
        ]);

        DB::beginTransaction();
        try {
            $penyewa->user->update([
                'name' => $request->nama_lengkap,
                'nama_lengkap' => $request->nama_lengkap,
                'no_hp' => $request->no_hp,
                'nik' => $request->nik,
            ]);

            $penyewa->update([
                'nama_usaha' => $request->nama_usaha,
                'alamat' => $request->alamat,
            ]);

            DB::commit();
            return redirect()
                ->route('adminkantin.penyewa.index')
                ->with('success', 'Data penyewa berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->with('error', 'Gagal memperbarui penyewa: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Penyewa $penyewa)
    {
        // Validasi: tidak boleh hapus penyewa jika masih memiliki sewa_ruko yang aktif / disetujui / menunggu
        $hasAktifSewa = $penyewa->sewaRuko()
            ->whereIn('status', ['disetujui', 'menunggu', 'pending'])
            ->exists();

        if ($hasAktifSewa) {
            return back()->with('error', 'Gagal! Penyewa tidak dapat dihapus karena masih memiliki sewa ruko/kantin yang berstatus aktif/berjalan.');
        }

        DB::beginTransaction();
        try {
            $user = $penyewa->user;

            // Hapus penyewa & user (akan cascade jika disetup di DB, tp kita hapus via eloquent agar aman)
            $penyewa->delete();
            if ($user) {
                // Relasi sewaRuko teknisnya terkait dengan users.id; tapi karena sudah dicek tdk ada yang aktif, aman.
                $user->delete();
            }

            DB::commit();
            return redirect()
                ->route('adminkantin.penyewa.index')
                ->with('success', 'Data penyewa berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat menghapus data penyewa: ' . $e->getMessage());
        }
    }
}
