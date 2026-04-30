<?php

namespace App\Http\Controllers\AdminServis;

use App\Http\Controllers\Controller;
use App\Models\PembayaranServis;
use App\Models\PengeluaranServis;
use Illuminate\Http\Request;
use Carbon\Carbon;

class KeuanganController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $tipe = $request->input('tipe');

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

        $totalPemasukan = $queryPemasukan->sum('total_biaya');
        $totalPengeluaran = $queryPengeluaran->sum('jumlah');
        $saldoAkhir = $totalPemasukan - $totalPengeluaran;

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

        $transaksi = $transaksi->sortByDesc('tanggal')->values();

        return view('adminservis.keuangan.index', compact(
            'totalPemasukan',
            'totalPengeluaran',
            'saldoAkhir',
            'transaksi'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jumlah' => 'required|numeric|min:0',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string|max:255'
        ]);

        PengeluaranServis::create([
            'jumlah' => $request->jumlah,
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan,
            'kategori' => 'operasional',
            'created_by' => auth()->id()
        ]);

        return redirect()->route('adminservis.keuangan.index')
            ->with('success', 'Data pengeluaran berhasil ditambahkan.');
    }
}
