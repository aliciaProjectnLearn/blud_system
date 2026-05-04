<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SewaRuko;
use App\Models\PembayaranRuko;
use App\Models\HistoryPembayaranRuko;
use App\Models\Ruko;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SewaTokenController extends Controller
{
    public function detail($token)
    {
        $sewa = SewaRuko::where('access_token', $token)->first();

        // Token tidak ditemukan
        if (!$sewa) {
            return view('user.kantin.token.invalid', [
                'pesan' => 'Link tidak valid atau tidak ditemukan.'
            ]);
        }

        // Token sudah expired (waktu)
        if ($sewa->token_expired_at && now()->isAfter($sewa->token_expired_at)) {
            return view('user.kantin.token.invalid', [
                'pesan' => 'Link akses ini sudah kadaluarsa.'
            ]);
        }

        // Jika booking sudah selesai, ditolak, atau dibatalkan, link tidak aktif lagi
        if (in_array($sewa->status_sewa, ['selesai', 'ditolak', 'dibatalkan'])) {
            return view('user.token.expired', ['status' => $sewa->status_sewa]);
        }

        // Lanjut ke verifikasi OTP
        if (!session('sewa_verified_' . $token)) {
            return redirect()->route('user.kantin.otp.form', $token);
        }

        $sewa->load(['ruko', 'ruko.kategori', 'user', 'pembayaran', 'dokumen']);
        return view('user.kantin.token.detail', compact('sewa'));
    }

    public function riwayat($token)
    {
        $sewa = SewaRuko::where('access_token', $token)->firstOrFail();
        $riwayat = SewaRuko::where('no_hp_snapshot', $sewa->no_hp_snapshot)
            ->with('ruko')
            ->orderBy('created_at', 'desc')
            ->get();
        
        $sewa_aktif = $riwayat->whereIn('status_sewa', ['pending', 'aktif']);
        $riwayat = $riwayat->whereNotIn('status_sewa', ['pending', 'aktif']);

        return view('user.kantin.token.riwayat', compact('sewa', 'sewa_aktif', 'riwayat'));
    }

    public function pembayaran(Request $request, $token)
    {
        $sewa = SewaRuko::with('pembayaran')->where('access_token', $token)->firstOrFail();
        
        $pembayaran_id = $request->query('pembayaran_id');
        if ($pembayaran_id) {
            $pembayaran = $sewa->pembayaran->where('id', $pembayaran_id)->first();
        } else {
            $pembayaran = $sewa->pembayaran->whereIn('status_pembayaran', ['pending', 'ditolak'])->sortBy('termin_ke')->first();
        }

        if (!$pembayaran) {
            return redirect()->route('user.kantin.sewa.detail', $token)->with('error', 'Tidak ada tagihan yang harus dibayar saat ini.');
        }

        $total = $sewa->pembayaran->sum('jumlah_tagihan');
        return view('user.kantin.token.upload-bukti', [
            'sewa' => $sewa,
            'pembayaran' => $pembayaran,
            'total' => $total
        ]);
    }

    public function uploadBukti(Request $request, $token)
    {
        $request->validate([
            'pembayaran_id' => 'required|exists:pembayaran_ruko,id',
            'bukti' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $sewa = SewaRuko::where('access_token', $token)->firstOrFail();
        $pembayaran = PembayaranRuko::where('sewa_ruko_id', $sewa->id)->findOrFail($request->pembayaran_id);

        if ($request->hasFile('bukti')) {
            $path = $request->file('bukti')->store('bukti-pembayaran', 'public');
            
            $metode = $request->metode_pembayaran;
            $tipe_id = null;
            if ($metode === 'transfer') $tipe_id = 1;
            elseif ($metode === 'tunai') $tipe_id = 2;
            elseif ($metode === 'qris') $tipe_id = 3;

            $pembayaran->update([
                'bukti_pembayaran' => $path,
                'status_pembayaran' => 'menunggu_verifikasi',
                'tanggal_bayar' => now(),
                'tipe_pembayaran_id' => $tipe_id,
            ]);

            HistoryPembayaranRuko::create([
                'pembayaran_ruko_id' => $pembayaran->id,
                'aksi' => 'upload_bukti',
                'keterangan' => 'User mengunggah bukti pembayaran untuk termin ke-' . $pembayaran->termin_ke,
                'dilakukan_oleh' => $sewa->nama_penyewa,
            ]);
        }

        return redirect()->route('user.kantin.sewa.detail', $token)->with('success', 'Bukti pembayaran berhasil diunggah.');
    }

    public function dokumen($token)
    {
        $sewa = SewaRuko::with('dokumen')->where('access_token', $token)->firstOrFail();
        return view('user.kantin.token.dokumen', compact('sewa'));
    }

    public function batalkan(Request $request, $token)
    {
        $sewa = SewaRuko::where('access_token', $token)->firstOrFail();

        if ($sewa->status_sewa !== 'pending') {
            return back()->with('error', 'Hanya sewa dengan status pending yang dapat dibatalkan.');
        }

        DB::beginTransaction();
        try {
            $sewa->update(['status_sewa' => 'dibatalkan']);
            
            $ruko = Ruko::find($sewa->ruko_id);
            if ($ruko) {
                $ruko->update(['status' => 'tersedia']);
            }

            HistoryPembayaranRuko::create([
                'aksi' => 'pembatalan',
                'keterangan' => 'Sewa dibatalkan oleh pengguna via token.',
                'dilakukan_oleh' => $sewa->nama_penyewa,
            ]);

            DB::commit();
            return redirect()->route('user.kantin.sewa.detail', $token)->with('success', 'Sewa berhasil dibatalkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membatalkan: ' . $e->getMessage());
        }
    }
}
