<?php

namespace App\Http\Controllers\AdminServis;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BookingServis;
use App\Models\PembayaranServis;
use App\Models\User;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class DashboardController extends Controller
{
    public function index()
    {
        $totalTransaksi = BookingServis::count();
        
        $totalPendapatan = PembayaranServis::whereMonth('tanggal_bayar', now()->month)
            ->whereYear('tanggal_bayar', now()->year)
            ->where('status_pembayaran', 'lunas')
            ->sum('total_biaya');
            
        $servisHariIni = BookingServis::whereDate('tanggal_booking', today())->count();
        
        $totalTeknisi = User::whereHas('roles', function($q) {
            $q->whereIn('nama', ['Teknisi Motor', 'Teknisi Mobil']);
        })->count();

        $pendapatanBulanan = [];
        for ($i = 1; $i <= 12; $i++) {
            $pendapatanBulanan[] = (int) PembayaranServis::whereMonth('tanggal_bayar', $i)
                ->whereYear('tanggal_bayar', now()->year)
                ->where('status_pembayaran', 'lunas')
                ->sum('total_biaya');
        }

        $transaksiTerbaru = BookingServis::with(['user', 'layananServis', 'teknisi', 'pembayaranServis', 'rincianServis'])
            ->latest()
            ->limit(10)
            ->get();

        $jadwalHariIni = BookingServis::with(['user', 'teknisi'])
            ->whereDate('tanggal_booking', today())
            ->orderBy('jam_booking', 'asc')
            ->get();

        return view('adminservis.index', compact(
            'totalTransaksi',
            'totalPendapatan',
            'servisHariIni',
            'totalTeknisi',
            'pendapatanBulanan',
            'transaksiTerbaru',
            'jadwalHariIni'
        ));
    }

    public function invoice($kode)
    {
        $booking = BookingServis::with(['user', 'layananServis', 'teknisi', 'pembayaranServis', 'rincianServis'])
            ->where('kode_booking', $kode)
            ->firstOrFail();

        return view('adminservis.invoice', compact('booking'));
    }

    public function downloadPdf($kode)
    {
        $booking = BookingServis::with(['user', 'layananServis', 'teknisi', 'pembayaranServis', 'rincianServis'])
            ->where('kode_booking', $kode)
            ->firstOrFail();

        $pdf = Pdf::loadView('adminservis.transaksi.invoice-pdf', compact('booking'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('Invoice-' . $kode . '.pdf');
    }
}
