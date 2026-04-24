<?php

namespace App\Http\Controllers\KasirServis;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PembayaranServis;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanKasirServisExport;

class LaporanController extends Controller
{
    /**
     * Tampilkan halaman laporan dan data yang difilter
     */
    public function index(Request $request)
    {
        $query = PembayaranServis::with([
            'bookingServis.pelanggan',
            'bookingServis.layananServis',
        ]);

        // Filter Tanggal Dari
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_bayar', '>=', $request->tanggal_dari);
        }

        // Filter Tanggal Sampai
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_bayar', '<=', $request->tanggal_sampai);
        }

        // Filter Metode Pembayaran
        if ($request->filled('metode_pembayaran')) {
            $query->where('tipe_pembayaran', $request->metode_pembayaran);
        }

        // Filter Status Pembayaran
        if ($request->filled('status')) {
            $query->where('status_pembayaran', $request->status);
        }

        // Default order
        $query->orderBy('tanggal_bayar', 'desc');

        // Untuk Summary
        $cloneQuery = clone $query;
        $totalPendapatan = (clone $cloneQuery)
            ->where('status_pembayaran', 'lunas')
            ->sum('total_biaya');
            
        $totalBooking = $cloneQuery->count();
        $totalLunas = (clone $cloneQuery)->where('status_pembayaran', 'lunas')->count();

        $laporan = $query->paginate(25)->withQueryString();

        return view('kasirservis.laporan.index', compact('laporan', 'totalPendapatan', 'totalBooking', 'totalLunas'));
    }

    /**
     * Export data ke format PDF
     */
    public function exportPdf(Request $request)
    {
        $query = PembayaranServis::with([
            'bookingServis.pelanggan',
            'bookingServis.layananServis',
        ]);

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_bayar', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_bayar', '<=', $request->tanggal_sampai);
        }

        if ($request->filled('metode_pembayaran')) {
            $query->where('tipe_pembayaran', $request->metode_pembayaran);
        }

        if ($request->filled('status')) {
            $query->where('status_pembayaran', $request->status);
        }

        $query->orderBy('tanggal_bayar', 'desc');

        $laporan = $query->get();
        $totalPendapatan = $laporan->where('status_pembayaran', 'lunas')->sum('total_biaya');

        $pdf = Pdf::loadView('kasirservis.laporan.pdf', compact('laporan', 'totalPendapatan'));
        
        $fileName = 'Laporan_Transaksi_Kasir_Servis_';
        if ($request->filled('tanggal_dari') && $request->filled('tanggal_sampai')) {
            $fileName .= $request->tanggal_dari . '_sd_' . $request->tanggal_sampai;
        } elseif ($request->filled('tanggal_dari')) {
            $fileName .= 'Sejak_' . $request->tanggal_dari;
        } elseif ($request->filled('tanggal_sampai')) {
            $fileName .= 'Hingga_' . $request->tanggal_sampai;
        } else {
            $fileName .= date('Y_m_d');
        }

        return $pdf->download($fileName . '.pdf');
    }

    /**
     * Export data ke format Excel
     */
    public function exportExcel(Request $request)
    {
        $fileName = 'Laporan_Transaksi_Kasir_Servis_';
        if ($request->filled('tanggal_dari') && $request->filled('tanggal_sampai')) {
            $fileName .= $request->tanggal_dari . '_sd_' . $request->tanggal_sampai;
        } elseif ($request->filled('tanggal_dari')) {
            $fileName .= 'Sejak_' . $request->tanggal_dari;
        } elseif ($request->filled('tanggal_sampai')) {
            $fileName .= 'Hingga_' . $request->tanggal_sampai;
        } else {
            $fileName .= date('Y_m_d');
        }

        return Excel::download(new LaporanKasirServisExport($request), $fileName . '.xlsx');
    }
}
