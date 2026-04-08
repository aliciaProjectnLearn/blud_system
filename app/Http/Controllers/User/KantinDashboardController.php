<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SewaRuko;
use App\Models\PembayaranRuko;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KantinDashboardController extends Controller
{
    public function index()
    {
        $user    = Auth::user();
        $penyewa = $user->penyewa;

        if (!$penyewa) {
            return view('user.kantin.dashboard', [
                'penyewa'          => null,
                'sewaAktif'        => null,
                'totalSewa'        => 0,
                'totalDikonfirmasi' => 0,
                'totalSelesai'     => 0,
                'tagihanMendatang' => null,
                'transaksiTerbaru' => collect(),
                'dataGrafik'       => [],
                'labelGrafik'      => [],
            ]);
        }

        $penyewaId = $penyewa->getKey();

        // ── Sewa aktif ──────────────────────────────────────
        $sewaAktif = SewaRuko::where('penyewa_id', $penyewaId)
            ->where('status', 'aktif')
            ->with(['ruko', 'pembayaran' => fn($q) => $q->orderBy('tgl_jatuh_tempo')])
            ->first();

        // ── Summary cards ───────────────────────────────────
        $totalSewa = SewaRuko::where('penyewa_id', $penyewaId)->count();

        $totalDikonfirmasi = PembayaranRuko::whereHas('sewaRuko', fn($q) => $q->where('penyewa_id', $penyewaId))
            ->where('status', 'verifikasi')
            ->count();

        $totalSelesai = PembayaranRuko::whereHas('sewaRuko', fn($q) => $q->where('penyewa_id', $penyewaId))
            ->where('status', 'lunas')
            ->count();

        // ── Tagihan mendatang (status menunggu, paling dekat jatuh tempo) ──
        $tagihanMendatang = PembayaranRuko::whereHas('sewaRuko', fn($q) => $q->where('penyewa_id', $penyewaId))
            ->where('status', 'menunggu')
            ->whereNotNull('tgl_jatuh_tempo')
            ->orderBy('tgl_jatuh_tempo')
            ->with('sewaRuko.ruko')
            ->first();

        // ── Transaksi terbaru (5 data) ──────────────────────
        $transaksiTerbaru = PembayaranRuko::whereHas('sewaRuko', fn($q) => $q->where('penyewa_id', $penyewaId))
            ->with('sewaRuko.ruko')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // ── Grafik tagihan per bulan (tahun ini) ────────────
        $grafik = PembayaranRuko::whereHas('sewaRuko', fn($q) => $q->where('penyewa_id', $penyewaId))
            ->whereYear('created_at', now()->year)
            ->select(
                DB::raw('MONTH(created_at) as bulan'),
                DB::raw('SUM(jumlah_tagihan) as total')
            )
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy('bulan')
            ->get();

        $labelGrafik  = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
        $dataGrafik   = array_fill(0, 12, 0);
        foreach ($grafik as $g) {
            $dataGrafik[$g->bulan - 1] = $g->total;
        }

        return view('user.kantin.dashboard', compact(
            'penyewa',
            'sewaAktif',
            'totalSewa',
            'totalDikonfirmasi',
            'totalSelesai',
            'tagihanMendatang',
            'transaksiTerbaru',
            'dataGrafik',
            'labelGrafik'
        ));
    }
    public function tagihan()
    {
        $user    = Auth::user();
        $penyewa = $user->penyewa;

        $tagihan = $penyewa
            ? PembayaranRuko::whereHas('sewaRuko', fn($q) => $q->where('penyewa_id', $penyewa->getKey()))
            ->where('status', 'menunggu')
            ->with('sewaRuko.ruko')
            ->orderBy('tgl_jatuh_tempo')
            ->get()
            : collect();

        return view('user.kantin.tagihan', compact('tagihan'));
    }

    public function riwayat()
    {
        $user    = Auth::user();
        $penyewa = $user->penyewa;

        $riwayat = $penyewa
            ? PembayaranRuko::whereHas('sewaRuko', fn($q) => $q->where('penyewa_id', $penyewa->getKey()))
            ->with('sewaRuko.ruko')
            ->orderBy('created_at', 'desc')
            ->get()
            : collect();

        return view('user.kantin.riwayat', compact('riwayat'));
    }
}
