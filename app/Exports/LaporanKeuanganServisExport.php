<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Http\Request;
use App\Models\PembayaranServis;
use App\Models\PengeluaranServis;
use Carbon\Carbon;

class LaporanKeuanganServisExport implements FromView, ShouldAutoSize
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    private function getTransaksiData()
    {
        $startDate = $this->request->input('start_date');
        $endDate = $this->request->input('end_date');
        $tipe = $this->request->input('tipe');

        $queryPemasukan = PembayaranServis::query();
        $queryPengeluaran = PengeluaranServis::query();

        if ($startDate && $endDate) {
            $queryPemasukan->whereBetween('tanggal_bayar', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);
            
            $queryPengeluaran->whereBetween('tanggal', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);
        }

        $transaksi = collect();

        if (!$tipe || $tipe === 'pemasukan') {
            $pemasukanData = $queryPemasukan->with('bookingServis.user')->latest('tanggal_bayar')->get()->map(function ($item) {
                return (object) [
                    'tanggal' => $item->tanggal_bayar,
                    'tipe' => 'pemasukan',
                    'deskripsi' => 'Pembayaran Servis - ' . ($item->bookingServis->user->name ?? 'Pelanggan'),
                    'nominal' => $item->total_biaya
                ];
            });
            $transaksi = $transaksi->merge($pemasukanData);
        }

        if (!$tipe || $tipe === 'pengeluaran') {
            $pengeluaranData = $queryPengeluaran->latest('tanggal')->get()->map(function ($item) {
                return (object) [
                    'tanggal' => $item->tanggal,
                    'tipe' => 'pengeluaran',
                    'deskripsi' => $item->keterangan,
                    'nominal' => $item->jumlah
                ];
            });
            $transaksi = $transaksi->merge($pengeluaranData);
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

        return view('adminservis.keuangan.excel', compact('transaksi', 'totalPemasukan', 'totalPengeluaran', 'saldoAkhir', 'request'));
    }
}
