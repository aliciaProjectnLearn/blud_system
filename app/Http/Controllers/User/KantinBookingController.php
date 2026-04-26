<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SewaRuko;
use App\Models\PembayaranRuko;
use App\Models\Ruko;
use App\Models\Booking;
use App\Models\Penyewa;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;

class KantinBookingController extends Controller
{
    /**
     * Tampilkan Katalog Layanan / Unit Kantin.
     */
    public function katalog()
    {
        $rukoList = Ruko::whereHas('kategori', function($q) {
                $q->where('tipe', 'kantin');
            })
            ->with(['kategori', 'dokumentasiUnit'])
            ->orderBy('kategori_id')
            ->orderBy('kode_unit')
            ->get();

        $kategoriList = \App\Models\Kategori::where('tipe', 'kantin')->get();

        $rukoDataJs = $rukoList;

        return view('user.kantin.katalog', compact('rukoList', 'kategoriList', 'rukoDataJs'));
    }

    /**
     * Tampilkan form booking langsung.
     */
    public function showBookingForm()
    {
        $units = Ruko::whereHas('kategori', fn($q) => $q->where('tipe', 'kantin'))
            ->where('status_unit', 'kosong')
            ->with(['kategori', 'dokumentasiUnit'])
            ->orderByRaw("CAST(SUBSTRING(kode_unit, 4) AS UNSIGNED) ASC")
            ->get();

        $user    = Auth::user();
        $penyewa = $user ? $user->penyewa : null;

        return view('user.kantin.booking-form', compact('units', 'user', 'penyewa'));
    }

    /**
     * Simpan pengajuan sewa baru.
     */
    public function storeBooking(Request $request)
    {
        $request->validate([
            'nama'              => 'required|string|max:255',
            'no_hp'             => 'required|string|max:20',
            'ruko_id'           => 'required|exists:ruko,id',
            'tgl_mulai'         => 'required|date|after_or_equal:today',
            'metode_pembayaran' => 'required|in:tunai,qris',
        ]);

        $ruko = Ruko::find($request->ruko_id);
        if (!$ruko || $ruko->status_unit !== 'kosong') {
            return back()->with('error', 'Unit sudah tidak tersedia.');
        }

        // Find or create user
        $user = User::where('no_hp', $request->no_hp)->first();
        if (!$user) {
            $user = User::create([
                'name' => explode(' ', $request->nama)[0],
                'nama_lengkap' => $request->nama,
                'no_hp' => $request->no_hp,
                'role' => 'pelanggan',
                'password' => bcrypt(Str::random(16)),
            ]);
            
            $role = Role::where('nama', 'pelanggan')->first();
            if ($role) {
                $user->roles()->attach($role->id);
            }
        }

        $accessToken = bin2hex(random_bytes(32));

        DB::beginTransaction();
        try {
            $penyewa = $user->penyewa;
            if (!$penyewa) {
                $penyewa = Penyewa::create([
                    'user_id'    => $user->id,
                    'nama_usaha' => $user->nama_lengkap,
                    'alamat'     => '-',
                ]);
            }

            $tglMulai   = Carbon::parse($request->tgl_mulai);
            $tglSelesai = $tglMulai->copy()->addYear();

            $booking = Booking::create([
                'user_id' => $user->id,
                'status'  => 'menunggu',
                'access_token' => $accessToken,
            ]);

            $sewaRuko = SewaRuko::create([
                'booking_id'         => $booking->id,
                'penyewa_id'         => $penyewa->id,
                'ruko_id'            => $ruko->id,
                'tgl_mulai'          => $tglMulai->format('Y-m-d'),
                'tgl_selesai'        => $tglSelesai->format('Y-m-d'),
                'harga_sewa_tahunan' => $ruko->harga,
                'status'             => 'pending',
                'access_token'      => $accessToken,
            ]);

            $jumlahPerTermin = intdiv((int) $ruko->harga, 2);
            $tipePembayaranId = \App\Models\TipePembayaran::where('nama', 'like', '%' . $request->metode_pembayaran . '%')
                ->value('id') ?? 1;

            PembayaranRuko::create([
                'sewa_ruko_id'       => $sewaRuko->id,
                'booking_id'         => $booking->id,
                'tipe_pembayaran_id' => $tipePembayaranId,
                'termin'             => 1,
                'tgl_jatuh_tempo'    => $tglMulai->format('Y-m-d'),
                'jumlah_tagihan'     => $jumlahPerTermin,
                'status'             => 'menunggu',
            ]);

            PembayaranRuko::create([
                'sewa_ruko_id'       => $sewaRuko->id,
                'booking_id'         => $booking->id,
                'tipe_pembayaran_id' => $tipePembayaranId,
                'termin'             => 2,
                'tgl_jatuh_tempo'    => $tglMulai->copy()->addMonths(6)->format('Y-m-d'),
                'jumlah_tagihan'     => $ruko->harga - $jumlahPerTermin,
                'status'             => 'menunggu',
            ]);

            $ruko->update(['status_unit' => 'terisi']);

            DB::commit();
            return redirect()->route('user.token.show', $accessToken)->with('success', 'Pengajuan sewa kantin berhasil!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function getUnitDetail($id)
    {
        $ruko = Ruko::with(['kategori', 'dokumentasiUnit'])
            ->where('status_unit', 'kosong')
            ->whereHas('kategori', fn($q) => $q->where('tipe', 'kantin'))
            ->find($id);

        if (!$ruko) {
            return response()->json(['error' => 'Unit tidak ditemukan.'], 404);
        }

        return response()->json([
            'id'        => $ruko->id,
            'kode_unit' => $ruko->kode_unit,
            'harga'       => $ruko->harga,
            'kategori'  => $ruko->kategori->nama ?? '-',
        ]);
    }
}
