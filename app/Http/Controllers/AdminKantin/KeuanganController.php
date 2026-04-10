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
        $tipe = $request->tipe;

        // Query Pemasukan (Pembayaran Ruko yang sudah diverifikasi)
        $queryPemasukan = PembayaranRuko::where('status', 'verifikasi');
        if ($tanggalMulai) {
            $queryPemasukan->whereDate('tgl_bayar', '>=', $tanggalMulai);
        }
        if ($tanggalSelesai) {
            $queryPemasukan->whereDate('tgl_bayar', '<=', $tanggalSelesai);
        }

        $pemasukan = $queryPemasukan->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'tanggal' => $item->tgl_bayar,
                'tipe' => 'pemasukan',
                'deskripsi' => 'Pembayaran Sewa ' . ($item->sewaRuko->ruko->kode_unit ?? '') . ' - Termin ' . $item->termin,
                'nominal' => $item->jumlah_tagihan,
            ];
        });

        // Query Pengeluaran
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
                'deskripsi' => $item->deskripsi,
                'nominal' => $item->nominal,
            ];
        });

        // Gabungkan
        $transaksi = $pemasukan->concat($pengeluaran);

        // Filter Tipe
        if ($tipe) {
            $transaksi = $transaksi->where('tipe', $tipe);
        }

        // Urutkan Tanggal Descending
        $transaksi = $transaksi->sortByDesc('tanggal')->values();

        // Hitung Totals
        $totalPemasukan = $pemasukan->sum('nominal');
        $totalPengeluaran = $pengeluaran->sum('nominal');
        $saldoAkhir = $totalPemasukan - $totalPengeluaran;

        return view('adminkantin.keuangan.index', compact(
            'transaksi',
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
        ]);

        PengeluaranKantin::create($request->all());

        return redirect()->back()->with('success', 'Pengeluaran berhasil dicatat.');
    }
}
