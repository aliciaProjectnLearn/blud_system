<?php

namespace App\Http\Controllers\AdminKantin;

use App\Http\Controllers\Controller;
use App\Models\Ruko;
use App\Models\SewaRuko;
use App\Models\PembayaranRuko;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Stats Cards
        $totalTersedia = Ruko::where('status', 'tersedia')->count();
        $totalDisewa = Ruko::where('status', 'disewa')->count();
        $totalAktif = SewaRuko::where('status_sewa', 'aktif')->count();
        $menungguVerifikasi = SewaRuko::where('status_sewa', 'pending')->count();
        
        $pendapatanBulanIni = PembayaranRuko::where('status_pembayaran', 'dibayar')
            ->whereMonth('tanggal_bayar', now()->month)
            ->whereYear('tanggal_bayar', now()->year)
            ->sum('jumlah_tagihan');

        // 2. Tabel Transaksi Terbaru
        $transaksiTerbaru = SewaRuko::with(['ruko', 'user'])
            ->latest()
            ->take(5)
            ->get();

        // 3. Jatuh Tempo Termin Bulan Ini (Pending)
        $jatuhTempoBulanIni = PembayaranRuko::with(['sewaRuko', 'sewaRuko.ruko'])
            ->where('status_pembayaran', 'pending')
            ->whereMonth('tgl_jatuh_tempo', now()->month)
            ->whereYear('tgl_jatuh_tempo', now()->year)
            ->whereHas('sewaRuko', function ($q) {
                $q->whereNotIn('status_sewa', ['dibatalkan', 'ditolak']);
            })
            ->get();

        // 4. Pengingat Jatuh Tempo Termin 2 (H-30) - Tetap dipertahankan untuk reminder WA
        $pengingatTermin2 = PembayaranRuko::with(['sewaRuko.user', 'sewaRuko.ruko'])
            ->where('termin_ke', 2)
            ->where('status_pembayaran', 'pending')
            ->whereBetween('tgl_jatuh_tempo', [now()->startOfDay(), now()->addDays(30)->endOfDay()])
            ->whereHas('sewaRuko', function ($q) {
                $q->whereNotIn('status_sewa', ['dibatalkan', 'ditolak']);
            })
            ->orderBy('tgl_jatuh_tempo', 'asc')
            ->get();

        return view('adminkantin.index', compact(
            'totalTersedia',
            'totalDisewa',
            'totalAktif',
            'menungguVerifikasi',
            'pendapatanBulanIni',
            'transaksiTerbaru',
            'jatuhTempoBulanIni',
            'pengingatTermin2'
        ));
    }

    public function kirimWaManual($id)
    {
        $pembayaran = PembayaranRuko::with(['sewaRuko.ruko'])->findOrFail($id);
        $sewa = $pembayaran->sewaRuko;
        $ruko = $sewa->ruko;

        // Gunakan snapshot columns agar data tetap akurat meskipun user mengubah profil
        $noHp = $sewa->no_hp_snapshot;
        $namaPenyewa = $sewa->nama_penyewa;

        if (!$noHp) {
            return back()->with('error', 'Nomor HP penyewa tidak ditemukan.');
        }

        $apiToken = config('services.fonnte.token');
        if (!$apiToken) {
            return back()->with('error', 'Token Fonnte belum diatur di .env.');
        }

        $kodeUnit = $ruko->kode_unit ?? '-';
        $tglJatuhTempo = Carbon::parse($pembayaran->tgl_jatuh_tempo)->translatedFormat('d F Y');
        $nominal = number_format($pembayaran->jumlah_tagihan, 0, ',', '.');

        $pesan = "Halo *{$namaPenyewa}* 👋\n\n";
        $pesan .= "Kami mengingatkan bahwa pembayaran sewa Termin 2 untuk unit *{$kodeUnit}* akan jatuh tempo pada *{$tglJatuhTempo}*.\n";
        $pesan .= "💰 *Jumlah tagihan: Rp {$nominal}*\n\n";
        $pesan .= "Mohon segera lakukan pembayaran sebelum tanggal tersebut untuk menghindari keterlambatan. Terima kasih 🙏\n";
        $pesan .= "— Admin Kantin BLUD SMK";

        try {
            $response = Http::withHeaders([
                'Authorization' => $apiToken,
            ])->post('https://api.fonnte.com/send', [
                'target'      => $noHp,
                'message'     => $pesan,
                'countryCode' => '62',
            ]);

            if ($response->successful()) {
                return back()->with('success', 'Pesan WhatsApp berhasil dikirim ke ' . $noHp);
            } else {
                return back()->with('error', 'Gagal mengirim WA: ' . $response->body());
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
