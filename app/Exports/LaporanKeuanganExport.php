<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Http\Request;
use App\Models\PembayaranRuko;
use App\Models\PengeluaranKantin;
use Carbon\Carbon;

class LaporanKeuanganExport implements FromView, ShouldAutoSize
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    private function getTransaksiData()
    {
        $tanggalMulai = $this->request->tanggal_mulai;
        $tanggalSelesai = $this->request->tanggal_selesai;
        $tipeFilter = $this->request->tipe;

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

        $transaksi = $pemasukan->concat($pengeluaran);
        if ($tipeFilter) {
            $transaksi = $transaksi->where('tipe', strtolower($tipeFilter));
        }
        return $transaksi->sortByDesc('tanggal')->values();
    }

    public function view(): View
    {
        $transaksi = $this->getTransaksiData();
        $totalPemasukan = $transaksi->where('tipe', 'pemasukan')->sum('nominal');
        $totalPengeluaran = $transaksi->where('tipe', 'pengeluaran')->sum('nominal');
        $saldoAkhir = $totalPemasukan - $totalPengeluaran;
        $request = $this->request;

        return view('adminkantin.keuangan.excel', compact('transaksi', 'totalPemasukan', 'totalPengeluaran', 'saldoAkhir', 'request'));
    }
}
