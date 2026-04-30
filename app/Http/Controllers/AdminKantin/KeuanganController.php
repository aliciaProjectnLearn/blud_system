<?php

namespace App\Http\Controllers\AdminKantin;

use App\Http\Controllers\Controller;
use App\Models\PembayaranRuko;
use App\Models\PengeluaranKantin;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class KeuanganController extends Controller
{
    public function index(Request $request)
    {
        $tanggalMulai = $request->tanggal_mulai;
        $tanggalSelesai = $request->tanggal_selesai;
        $tipeFilter = $request->tipe;

        // 1. Pemasukan (Grouped by Month for Summary)
        $pemasukanGrouped = PembayaranRuko::where('status_pembayaran', 'dibayar')
            ->selectRaw('MONTH(tanggal_bayar) as bulan, YEAR(tanggal_bayar) as tahun, SUM(jumlah_tagihan) as total')
            ->groupBy('tahun', 'bulan')
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->get();

        // 2. Query Detail Transaksi untuk Tabel
        // Pemasukan Detail
        $queryPemasukan = PembayaranRuko::where('status_pembayaran', 'dibayar');
        if ($tanggalMulai) {
            $queryPemasukan->whereDate('tanggal_bayar', '>=', $tanggalMulai);
        }
        if ($tanggalSelesai) {
            $queryPemasukan->whereDate('tanggal_bayar', '<=', $tanggalSelesai);
        }

        $pemasukan = $queryPemasukan->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'tanggal' => $item->tanggal_bayar,
                'tipe' => 'pemasukan',
                'deskripsi' => 'Pembayaran Sewa ' . ($item->sewaRuko->ruko->kode_unit ?? '') . ' - Termin ' . $item->termin_ke,
                'nominal' => $item->jumlah_tagihan,
            ];
        });

        // Pengeluaran Detail
        $queryPengeluaran = PengeluaranKantin::query();
        if ($tanggalMulai) {
            $queryPengeluaran->whereDate('tanggal', '>=', $tanggalMulai);
        }
        if ($tanggalSelesai) {
            $queryPengeluaran->whereDate('tanggal', '<=', $tanggalSelesai);
        }

        $pengeluaran = $queryPengeluaran->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'tanggal' => $item->tanggal,
                'tipe' => 'pengeluaran',
                'deskripsi' => '[' . ucfirst($item->kategori_pengeluaran) . '] ' . $item->deskripsi,
                'nominal' => $item->nominal,
            ];
        });

        // Gabungkan dan Filter
        $transaksi = $pemasukan->concat($pengeluaran);
        if ($tipeFilter) {
            $transaksi = $transaksi->where('tipe', $tipeFilter);
        }
        $transaksi = $transaksi->sortByDesc('tanggal')->values();

        // 3. Hitung Totals Keseluruhan (Bukan hanya filter)
        $totalPemasukan = PembayaranRuko::where('status_pembayaran', 'dibayar')->sum('jumlah_tagihan');
        $totalPengeluaran = PengeluaranKantin::sum('nominal');
        $saldoAkhir = $totalPemasukan - $totalPengeluaran;

        return view('adminkantin.keuangan.index', compact(
            'transaksi',
            'pemasukanGrouped',
            'totalPemasukan',
            'totalPengeluaran',
            'saldoAkhir'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nominal' => 'required|numeric|min:0',
            'deskripsi' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'kategori_pengeluaran' => 'required|in:pemeliharaan,operasional,lainnya',
        ]);

        PengeluaranKantin::create($request->all());

        return redirect()->back()->with('success', 'Pengeluaran berhasil dicatat.');
    }
}
