<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RekapKeuanganController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->start_date;
        $endDate   = $request->end_date;

        // ── PEMASUKAN PER SISTEM ───────────────────────────────────

        $pemasukanFutsal = (float) DB::table('pembayaran_futsal')
            ->where('status', 'verifikasi')
            ->when($startDate, fn($q) => $q->whereDate('tgl_bayar', '>=', $startDate))
            ->when($endDate,   fn($q) => $q->whereDate('tgl_bayar', '<=', $endDate))
            ->sum('jumlah_bayar');

        $pemasukanKantin = (float) DB::table('pembayaran_ruko')
            ->where('status', 'verifikasi')
            ->when($startDate, fn($q) => $q->whereDate('tgl_bayar', '>=', $startDate))
            ->when($endDate,   fn($q) => $q->whereDate('tgl_bayar', '<=', $endDate))
            ->sum('jumlah_tagihan');

        $pemasukanAc = (float) DB::table('pembayaran_ac')
            ->where('status', 'dibayar')
            ->when($startDate, fn($q) => $q->whereDate('tgl_bayar', '>=', $startDate))
            ->when($endDate,   fn($q) => $q->whereDate('tgl_bayar', '<=', $endDate))
            ->sum('total_harga');

        $pemasukanServis = (float) DB::table('pembayaran_servis')
            ->where('status_pembayaran', 'lunas')
            ->when($startDate, fn($q) => $q->whereDate('tanggal_bayar', '>=', $startDate))
            ->when($endDate,   fn($q) => $q->whereDate('tanggal_bayar', '<=', $endDate))
            ->sum('total_biaya');

        // ── PENGELUARAN PER SISTEM ─────────────────────────────────

        $pengeluaranFutsal = (float) DB::table('pengeluaran_futsals')
            ->when($startDate, fn($q) => $q->whereDate('tgl_pengeluaran', '>=', $startDate))
            ->when($endDate,   fn($q) => $q->whereDate('tgl_pengeluaran', '<=', $endDate))
            ->sum('nominal');

        $pengeluaranKantin = (float) DB::table('pengeluaran_kantin')
            ->when($startDate, fn($q) => $q->whereDate('tanggal', '>=', $startDate))
            ->when($endDate,   fn($q) => $q->whereDate('tanggal', '<=', $endDate))
            ->sum('nominal');

        $pengeluaranAc = (float) DB::table('pengeluaran_ac')
            ->when($startDate, fn($q) => $q->whereDate('tanggal', '>=', $startDate))
            ->when($endDate,   fn($q) => $q->whereDate('tanggal', '<=', $endDate))
            ->sum('nominal');

        $pengeluaranServis = (float) DB::table('pengeluaran_servis')
            ->when($startDate, fn($q) => $q->whereDate('tanggal', '>=', $startDate))
            ->when($endDate,   fn($q) => $q->whereDate('tanggal', '<=', $endDate))
            ->sum('jumlah');

        // ── TOTAL KESELURUHAN ──────────────────────────────────────

        $totalPemasukan   = $pemasukanFutsal + $pemasukanKantin + $pemasukanAc + $pemasukanServis;
        $totalPengeluaran = $pengeluaranFutsal + $pengeluaranKantin + $pengeluaranAc + $pengeluaranServis;
        $saldoAkhir       = $totalPemasukan - $totalPengeluaran;

        // ── BREAKDOWN PER SISTEM (Pemasukan, Pengeluaran, Saldo Bersih) ──

        $breakdown = [
            'futsal' => [
                'pemasukan'   => $pemasukanFutsal,
                'pengeluaran' => $pengeluaranFutsal,
                'saldo'       => $pemasukanFutsal - $pengeluaranFutsal,
            ],
            'kantin' => [
                'pemasukan'   => $pemasukanKantin,
                'pengeluaran' => $pengeluaranKantin,
                'saldo'       => $pemasukanKantin - $pengeluaranKantin,
            ],
            'ac' => [
                'pemasukan'   => $pemasukanAc,
                'pengeluaran' => $pengeluaranAc,
                'saldo'       => $pemasukanAc - $pengeluaranAc,
            ],
            'servis' => [
                'pemasukan'   => $pemasukanServis,
                'pengeluaran' => $pengeluaranServis,
                'saldo'       => $pemasukanServis - $pengeluaranServis,
            ],
        ];

        // ── DATA GRAFIK (per bulan, 6 bulan terakhir) ────────────

        $grafik = [];
        for ($i = 5; $i >= 0; $i--) {
            $bulan      = now()->subMonths($i);
            $labelBulan = $bulan->translatedFormat('M Y');

            $masuk = (float) DB::table('pembayaran_futsal')->where('status', 'verifikasi')
                ->whereYear('tgl_bayar', $bulan->year)->whereMonth('tgl_bayar', $bulan->month)->sum('jumlah_bayar')
                + (float) DB::table('pembayaran_ruko')->where('status', 'verifikasi')
                ->whereYear('tgl_bayar', $bulan->year)->whereMonth('tgl_bayar', $bulan->month)->sum('jumlah_tagihan')
                + (float) DB::table('pembayaran_ac')->where('status', 'dibayar')
                ->whereYear('tgl_bayar', $bulan->year)->whereMonth('tgl_bayar', $bulan->month)->sum('total_harga')
                + (float) DB::table('pembayaran_servis')->where('status_pembayaran', 'lunas')
                ->whereYear('tanggal_bayar', $bulan->year)->whereMonth('tanggal_bayar', $bulan->month)->sum('total_biaya');

            $keluar = (float) DB::table('pengeluaran_futsals')
                ->whereYear('tgl_pengeluaran', $bulan->year)->whereMonth('tgl_pengeluaran', $bulan->month)->sum('nominal')
                + (float) DB::table('pengeluaran_kantin')
                ->whereYear('tanggal', $bulan->year)->whereMonth('tanggal', $bulan->month)->sum('nominal')
                + (float) DB::table('pengeluaran_ac')
                ->whereYear('tanggal', $bulan->year)->whereMonth('tanggal', $bulan->month)->sum('nominal')
                + (float) DB::table('pengeluaran_servis')
                ->whereYear('tanggal', $bulan->year)->whereMonth('tanggal', $bulan->month)->sum('jumlah');

            $grafik[] = [
                'bulan'       => $labelBulan,
                'pemasukan'   => $masuk,
                'pengeluaran' => $keluar,
            ];
        }

        return view('dashboard.rekap-keuangan', compact(
            'totalPemasukan',
            'totalPengeluaran',
            'saldoAkhir',
            'breakdown',
            'grafik',
            'startDate',
            'endDate'
        ));
    }
}
