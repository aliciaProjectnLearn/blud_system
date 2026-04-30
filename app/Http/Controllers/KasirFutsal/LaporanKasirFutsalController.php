<?php

namespace App\Http\Controllers\KasirFutsal;

use App\Http\Controllers\Controller;
use App\Models\PembayaranFutsal;
use App\Models\BookingFutsal;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class LaporanKasirFutsalController extends Controller
{
    public function index(Request $request)
    {
        $query = PembayaranFutsal::query();

        // Filter Tanggal
        if ($request->filled('tanggal')) {
            $query->whereDate('tgl_bayar', $request->tanggal);
        } else {
            // Default hari ini
            $query->whereDate('tgl_bayar', today());
        }

        $query->orderBy('tgl_bayar', 'desc');

        $laporan = $query->paginate(25)->withQueryString();

        foreach ($laporan as $pembayaran) {
            $pembayaran->bookingFutsal = BookingFutsal::with(['user', 'lapangan'])
                ->where('booking_id', $pembayaran->booking_id)
                ->first();
            $pembayaran->load('tipePembayaran');
        }

        $totalPendapatan = (clone $query)->where('status', PembayaranFutsal::STATUS_VERIFIKASI)->sum('jumlah_bayar');

        return view('kasirfutsal.laporan.index', compact('laporan', 'totalPendapatan'));
    }

    public function exportPdf(Request $request)
    {
        $query = PembayaranFutsal::query();

        if ($request->filled('tanggal')) {
            $query->whereDate('tgl_bayar', $request->tanggal);
            $tanggalLabel = $request->tanggal;
        } else {
            $query->whereDate('tgl_bayar', today());
            $tanggalLabel = today()->format('Y-m-d');
        }

        $query->orderBy('tgl_bayar', 'desc');

        $laporan = $query->get();

        foreach ($laporan as $pembayaran) {
            $pembayaran->bookingFutsal = BookingFutsal::with(['user', 'lapangan'])
                ->where('booking_id', $pembayaran->booking_id)
                ->first();
        }

        $totalPendapatan = $laporan->where('status', PembayaranFutsal::STATUS_VERIFIKASI)->sum('jumlah_bayar');

        $pdf = Pdf::loadView('kasirfutsal.laporan.pdf', compact('laporan', 'totalPendapatan', 'tanggalLabel'));
        
        $fileName = 'Laporan_Transaksi_Kasir_Futsal_' . $tanggalLabel . '.pdf';

        return $pdf->download($fileName);
    }
}
