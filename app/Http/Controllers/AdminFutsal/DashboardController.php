<?php

namespace App\Http\Controllers\AdminFutsal;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // ── Total Transaksi Futsal ──────────────────────────
        $totalTransaksi = DB::table('pembayaran_futsal')->count();

        // ── Total Pendapatan Futsal (status verifikasi) ─────
        $totalPendapatan = DB::table('pembayaran_futsal')
            ->where('status', 'verifikasi')
            ->sum('jumlah_bayar');

        // ── Transaksi Hari Ini ──────────────────────────────
        $transaksiHariIni = DB::table('pembayaran_futsal')
            ->whereDate('tgl_bayar', $today)
            ->count();

        // ── Status Pembayaran ───────────────────────────────
        $statusMenunggu   = DB::table('pembayaran_futsal')->where('status', 'menunggu')->count();
        $statusVerifikasi = DB::table('pembayaran_futsal')->where('status', 'verifikasi')->count();
        $statusDibatalkan = DB::table('pembayaran_futsal')->where('status', 'dibatalkan')->count();

        // ── Transaksi Terbaru ───────────────────────────────
        $transaksiTerbaru = DB::table('pembayaran_futsal')
            ->join('booking', 'pembayaran_futsal.booking_id', '=', 'booking.id')
            ->join('users', 'booking.user_id', '=', 'users.id')
            ->select(
                'users.name as nama_user',
                'pembayaran_futsal.jumlah_bayar',
                'pembayaran_futsal.status',
                'pembayaran_futsal.tgl_bayar'
            )
            ->orderBy('pembayaran_futsal.tgl_bayar', 'desc')
            ->limit(5)
            ->get();

        // ── Jadwal Lapangan Hari Ini ────────────────────────
        $jadwalHariIni = \App\Models\JadwalLapangan::with('lapangan')
            ->whereDate('tanggal', $today)
            ->orderBy('jam_mulai', 'asc')
            ->get();

        // ── Pendapatan Per Bulan (12 bulan terakhir) ────────
        $pendapatanPerBulan = DB::table('pembayaran_futsal')
            ->where('status', 'verifikasi')
            ->whereYear('tgl_bayar', $today->year)
            ->select(
                DB::raw('MONTH(tgl_bayar) as bulan'),
                DB::raw('SUM(jumlah_bayar) as total')
            )
            ->groupBy(DB::raw('MONTH(tgl_bayar)'))
            ->orderBy('bulan')
            ->get();

        // Format data grafik: pastikan semua 12 bulan ada
        $labelBulan = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'];
        $dataPendapatan = array_fill(0, 12, 0);
        foreach ($pendapatanPerBulan as $p) {
            $dataPendapatan[$p->bulan - 1] = $p->total;
        }

        return view('adminfutsal.index', compact(
            'totalTransaksi',
            'totalPendapatan',
            'transaksiHariIni',
            'statusMenunggu',
            'statusVerifikasi',
            'statusDibatalkan',
            'transaksiTerbaru',
            'jadwalHariIni',
            'labelBulan',
            'dataPendapatan'
        ));
    }
}
