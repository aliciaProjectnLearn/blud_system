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

        // ── PEMASUKAN ─────────────────────────────────────────────

        // Futsal — hanya status verifikasi
        $pemasukanFutsal = DB::table('pembayaran_futsal')
            ->where('status', 'verifikasi')
            ->when($startDate, fn($q) => $q->whereDate('tgl_bayar', '>=', $startDate))
            ->when($endDate,   fn($q) => $q->whereDate('tgl_bayar', '<=', $endDate))
            ->sum('jumlah_bayar');

        // Kantin/Ruko — hanya status verifikasi
        $pemasukanKantin = DB::table('pembayaran_ruko')
            ->where('status', 'verifikasi')
            ->when($startDate, fn($q) => $q->whereDate('tgl_bayar', '>=', $startDate))
            ->when($endDate,   fn($q) => $q->whereDate('tgl_bayar', '<=', $endDate))
            ->sum('jumlah_tagihan');

        // AC — hanya status dibayar
        $pemasukanAc = DB::table('pembayaran_ac')
            ->where('status', 'dibayar')
            ->when($startDate, fn($q) => $q->whereDate('tgl_bayar', '>=', $startDate))
            ->when($endDate,   fn($q) => $q->whereDate('tgl_bayar', '<=', $endDate))
            ->sum('total_harga');

        // ── PENGELUARAN ───────────────────────────────────────────

        $pengeluaranKantin = DB::table('pengeluaran_kantin')
            ->when($startDate, fn($q) => $q->whereDate('tanggal', '>=', $startDate))
            ->when($endDate,   fn($q) => $q->whereDate('tanggal', '<=', $endDate))
            ->sum('nominal');

        $pengeluaranAc = DB::table('pengeluaran_ac')
            ->when($startDate, fn($q) => $q->whereDate('tanggal', '>=', $startDate))
            ->when($endDate,   fn($q) => $q->whereDate('tanggal', '<=', $endDate))
            ->sum('nominal');

        $pengeluaranFutsal = DB::table('pengeluaran_futsals')
            ->when($startDate, fn($q) => $q->whereDate('tgl_pengeluaran', '>=', $startDate))
            ->when($endDate,   fn($q) => $q->whereDate('tgl_pengeluaran', '<=', $endDate))
            ->sum('nominal');

        // ── TOTAL ─────────────────────────────────────────────────

        $totalPemasukan   = $pemasukanFutsal + $pemasukanKantin + $pemasukanAc;
        $totalPengeluaran = $pengeluaranFutsal + $pengeluaranKantin + $pengeluaranAc;
        $saldoAkhir       = $totalPemasukan - $totalPengeluaran;

        // ── BREAKDOWN PER SISTEM ──────────────────────────────────

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
        ];

        // ── DAFTAR TRANSAKSI GABUNGAN ─────────────────────────────

        // Pemasukan Futsal
        $listFutsalIn = DB::table('pembayaran_futsal')
            ->where('status', 'verifikasi')
            ->when($startDate, fn($q) => $q->whereDate('tgl_bayar', '>=', $startDate))
            ->when($endDate,   fn($q) => $q->whereDate('tgl_bayar', '<=', $endDate))
            ->select(
                'id',
                DB::raw("'Futsal' as sistem"),
                DB::raw("'Pemasukan' as tipe"),
                'jumlah_bayar as nominal',
                'tgl_bayar as tanggal',
                DB::raw("CONCAT('Pembayaran Futsal #', id) as deskripsi"),
                'created_at'
            );

        // Pemasukan Kantin
        $listKantinIn = DB::table('pembayaran_ruko')
            ->where('status', 'verifikasi')
            ->when($startDate, fn($q) => $q->whereDate('tgl_bayar', '>=', $startDate))
            ->when($endDate,   fn($q) => $q->whereDate('tgl_bayar', '<=', $endDate))
            ->select(
                'id',
                DB::raw("'Kantin' as sistem"),
                DB::raw("'Pemasukan' as tipe"),
                'jumlah_tagihan as nominal',
                'tgl_bayar as tanggal',
                DB::raw("CONCAT('Pembayaran Kantin #', id) as deskripsi"),
                'created_at'
            );

        // Pemasukan AC
        $listAcIn = DB::table('pembayaran_ac')
            ->where('status', 'dibayar')
            ->when($startDate, fn($q) => $q->whereDate('tgl_bayar', '>=', $startDate))
            ->when($endDate,   fn($q) => $q->whereDate('tgl_bayar', '<=', $endDate))
            ->select(
                'id',
                DB::raw("'AC' as sistem"),
                DB::raw("'Pemasukan' as tipe"),
                'total_harga as nominal',
                'tgl_bayar as tanggal',
                DB::raw("CONCAT('Pembayaran AC #', id) as deskripsi"),
                'created_at'
            );

        // Pengeluaran Futsal
        $listFutsalOut = DB::table('pengeluaran_futsals')
            ->when($startDate, fn($q) => $q->whereDate('tgl_pengeluaran', '>=', $startDate))
            ->when($endDate,   fn($q) => $q->whereDate('tgl_pengeluaran', '<=', $endDate))
            ->select(
                'id',
                DB::raw("'Futsal' as sistem"),
                DB::raw("'Pengeluaran' as tipe"),
                'nominal',
                'tgl_pengeluaran as tanggal',
                'deskripsi',
                'created_at'
            );

        // Pengeluaran Kantin
        $listKantinOut = DB::table('pengeluaran_kantin')
            ->when($startDate, fn($q) => $q->whereDate('tanggal', '>=', $startDate))
            ->when($endDate,   fn($q) => $q->whereDate('tanggal', '<=', $endDate))
            ->select(
                'id',
                DB::raw("'Kantin' as sistem"),
                DB::raw("'Pengeluaran' as tipe"),
                'nominal',
                'tanggal as tanggal',
                'deskripsi',
                'created_at'
            );

        // Pengeluaran AC
        $listAcOut = DB::table('pengeluaran_ac')
            ->when($startDate, fn($q) => $q->whereDate('tanggal', '>=', $startDate))
            ->when($endDate,   fn($q) => $q->whereDate('tanggal', '<=', $endDate))
            ->select(
                'id',
                DB::raw("'AC' as sistem"),
                DB::raw("'Pengeluaran' as tipe"),
                'nominal',
                'tanggal as tanggal',
                'deskripsi',
                'created_at'
            );

        $transaksi = $listFutsalIn
            ->unionAll($listKantinIn)
            ->unionAll($listAcIn)
            ->unionAll($listFutsalOut)
            ->unionAll($listKantinOut)
            ->unionAll($listAcOut);

        $daftarTransaksi = DB::query()
            ->fromSub($transaksi, 'rekap')
            ->orderByDesc('tanggal')
            ->paginate(15)
            ->withQueryString();

        // ── DATA GRAFIK (per bulan, 6 bulan terakhir) ────────────

        $grafik = [];
        for ($i = 5; $i >= 0; $i--) {
            $bulan      = now()->subMonths($i);
            $labelBulan = $bulan->translatedFormat('M Y');

            $masuk = DB::table('pembayaran_futsal')->where('status', 'verifikasi')
                ->whereYear('tgl_bayar', $bulan->year)->whereMonth('tgl_bayar', $bulan->month)->sum('jumlah_bayar')
                + DB::table('pembayaran_ruko')->where('status', 'verifikasi')
                ->whereYear('tgl_bayar', $bulan->year)->whereMonth('tgl_bayar', $bulan->month)->sum('jumlah_tagihan')
                + DB::table('pembayaran_ac')->where('status', 'dibayar')
                ->whereYear('tgl_bayar', $bulan->year)->whereMonth('tgl_bayar', $bulan->month)->sum('total_harga');

            $keluar = DB::table('pengeluaran_futsals')
                ->whereYear('tgl_pengeluaran', $bulan->year)->whereMonth('tgl_pengeluaran', $bulan->month)->sum('nominal')
                + DB::table('pengeluaran_kantin')
                ->whereYear('tanggal', $bulan->year)->whereMonth('tanggal', $bulan->month)->sum('nominal')
                + DB::table('pengeluaran_ac')
                ->whereYear('tanggal', $bulan->year)->whereMonth('tanggal', $bulan->month)->sum('nominal');

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
            'daftarTransaksi',
            'grafik',
            'startDate',
            'endDate'
        ));
    }
}
