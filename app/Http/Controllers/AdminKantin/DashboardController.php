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
        // Total pendapatan (pembayaran yang sudah verifikasi)
        $totalPendapatan = PembayaranRuko::where('status', 'verifikasi')->sum('jumlah_tagihan');

        // Total penyewa aktif
        $totalPenyewa = SewaRuko::where('status', 'disetujui')->count();

        // Total unit terisi & keseluruhan
        $totalUnitTerisi = Ruko::where('status_unit', 'terisi')->count();
        $totalUnit       = Ruko::count();

        // Status pembayaran per termin
        $statusTermin = [
            'termin1_menunggu'   => PembayaranRuko::where('termin', 1)->where('status', 'menunggu')->count(),
            'termin1_verifikasi' => PembayaranRuko::where('termin', 1)->where('status', 'verifikasi')->count(),
            'termin1_dibatalkan' => PembayaranRuko::where('termin', 1)->where('status', 'dibatalkan')->count(),
            'termin2_menunggu'   => PembayaranRuko::where('termin', 2)->where('status', 'menunggu')->count(),
            'termin2_verifikasi' => PembayaranRuko::where('termin', 2)->where('status', 'verifikasi')->count(),
            'termin2_dibatalkan' => PembayaranRuko::where('termin', 2)->where('status', 'dibatalkan')->count(),
        ];

        // Status pembayaran keseluruhan (untuk progress bar)
        $statusVerifikasi = PembayaranRuko::where('status', 'verifikasi')->count();
        $statusMenunggu   = PembayaranRuko::where('status', 'menunggu')->count();
        $statusDibatalkan = PembayaranRuko::where('status', 'dibatalkan')->count();
        $totalTransaksi   = PembayaranRuko::count();


        // Transaksi terbaru
        $transaksiTerbaru = PembayaranRuko::with(['sewaRuko.penyewa.user', 'sewaRuko.ruko.kategori'])
            ->latest()
            ->take(5)
            ->get();

        // Pengingat Jatuh Tempo Termin 2 (H-30)
        $pengingatTermin2 = PembayaranRuko::with(['sewaRuko.penyewa.user', 'sewaRuko.ruko'])
            ->where('termin', 2)
            ->where('status', 'menunggu')
            ->whereBetween('tgl_jatuh_tempo', [now()->startOfDay(), now()->addDays(30)->endOfDay()])
            ->orderBy('tgl_jatuh_tempo', 'asc')
            ->get();

        return view('adminkantin.index', compact(
            'totalPendapatan',
            'totalPenyewa',
            'totalUnitTerisi',
            'totalUnit',
            'statusTermin',
            'statusVerifikasi',
            'statusMenunggu',
            'statusDibatalkan',
            'totalTransaksi',
            'transaksiTerbaru',
            'pengingatTermin2',
        ));
    }

    public function kirimWaManual($id)
    {
        $pembayaran = PembayaranRuko::with(['sewaRuko.penyewa.user', 'sewaRuko.ruko'])->findOrFail($id);
        $sewa = $pembayaran->sewaRuko;
        $user = $sewa->penyewa->user ?? null;
        $ruko = $sewa->ruko;

        if (!$user || !$user->no_hp) {
            return back()->with('error', 'Nomor HP penyewa tidak ditemukan.');
        }

        $apiToken = env('FONNTE_TOKEN', 'YOUR_API_TOKEN_HERE');
        if ($apiToken === 'YOUR_API_TOKEN_HERE') {
            return back()->with('error', 'Token Fonnte belum diatur di .env.');
        }

        $namaPenyewa = $user->nama_lengkap ?? $user->name;
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
                'target'      => $user->no_hp,
                'message'     => $pesan,
                'countryCode' => '62',
            ]);

            if ($response->successful()) {
                $sewa->update(['notifikasi_terkirim' => true]);
                return back()->with('success', 'Pesan WhatsApp berhasil dikirim ke ' . $user->no_hp);
            } else {
                return back()->with('error', 'Gagal mengirim WA: ' . $response->body());
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
