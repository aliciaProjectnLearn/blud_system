<?php

namespace App\Http\Controllers\AdminAc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PembayaranAc;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanAcExport;

class LaporanController extends Controller
{
    /**
     * Tampilkan halaman laporan dan data yang difilter
     */
    public function index(Request $request)
    {
        $query = PembayaranAc::with([
            'bookingAc.user',
            'bookingAc.layanan',
            'bookingAc.teknisi',
            'tipePembayaran'
        ]);

        if ($request->filled('tanggal_dari') && $request->filled('tanggal_sampai')) {
            $query->whereBetween('tgl_bayar', [$request->tanggal_dari . ' 00:00:00', $request->tanggal_sampai . ' 23:59:59']);
        } elseif ($request->filled('tanggal_dari')) {
            $query->where('tgl_bayar', '>=', $request->tanggal_dari . ' 00:00:00');
        } elseif ($request->filled('tanggal_sampai')) {
            $query->where('tgl_bayar', '<=', $request->tanggal_sampai . ' 23:59:59');
        }

        $query->orderBy('tgl_bayar', 'desc');

        $totalPendapatan = (clone $query)
            ->where('status', 'dibayar')
            ->sum('total_harga');

        $laporan = $query->paginate(25)->withQueryString();

        return view('adminac.laporan.index', compact('laporan', 'totalPendapatan'));
    }

    /**
     * Export data ke format PDF
     */
    public function exportPdf(Request $request)
    {
        $query = PembayaranAc::with([
            'bookingAc.user',
            'bookingAc.layanan',
            'bookingAc.teknisi',
            'tipePembayaran'
        ]);

        if ($request->filled('tanggal_dari') && $request->filled('tanggal_sampai')) {
            $query->whereBetween('tgl_bayar', [$request->tanggal_dari . ' 00:00:00', $request->tanggal_sampai . ' 23:59:59']);
        } elseif ($request->filled('tanggal_dari')) {
            $query->where('tgl_bayar', '>=', $request->tanggal_dari . ' 00:00:00');
        } elseif ($request->filled('tanggal_sampai')) {
            $query->where('tgl_bayar', '<=', $request->tanggal_sampai . ' 23:59:59');
        }

        $query->orderBy('tgl_bayar', 'desc');

        $laporan = $query->get();
        $totalPendapatan = $laporan->where('status', 'dibayar')->sum('total_harga');

        $pdf = Pdf::loadView('adminac.laporan.pdf', compact('laporan', 'totalPendapatan'));
        
        $fileName = 'Laporan_Transaksi_AC_';
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
        $fileName = 'Laporan_Transaksi_AC_';
        if ($request->filled('tanggal_dari') && $request->filled('tanggal_sampai')) {
            $fileName .= $request->tanggal_dari . '_sd_' . $request->tanggal_sampai;
        } elseif ($request->filled('tanggal_dari')) {
            $fileName .= 'Sejak_' . $request->tanggal_dari;
        } elseif ($request->filled('tanggal_sampai')) {
            $fileName .= 'Hingga_' . $request->tanggal_sampai;
        } else {
            $fileName .= date('Y_m_d');
        }

        return Excel::download(new LaporanAcExport($request), $fileName . '.xlsx');
    }
}
