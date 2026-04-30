<?php

namespace App\Http\Controllers\AdminServis;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\PembayaranServis;
use App\Models\BookingServis;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanServisExport;

class LaporanController extends Controller
{
    /**
     * Tampilkan halaman laporan transaksi servis beserta statistik
     */
    public function index(Request $request)
    {
        $query = PembayaranServis::with([
            'bookingServis.pelanggan',
            'bookingServis.layananServis',
            'bookingServis.teknisi',
            'bookingServis.rincianServis',
        ]);

        // Filter tanggal_dari
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_bayar', '>=', $request->tanggal_dari);
        }

        // Filter tanggal_sampai
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_bayar', '<=', $request->tanggal_sampai);
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status_pembayaran', $request->status);
        }

        // Hitung statistik sebelum paginate (clone query)
        $totalPendapatan = (clone $query)
            ->where('status_pembayaran', 'lunas')
            ->sum('total_biaya');

        $totalTransaksi = (clone $query)->count();

        $totalBooking = (clone $query)
            ->distinct('booking_servis_id')
            ->count('booking_servis_id');

        // Layanan terpopuler
        $layananTerpopuler = BookingServis::selectRaw('layanan_servis_id, COUNT(*) as total')
            ->with('layananServis')
            ->whereHas('pembayaranServis')
            ->groupBy('layanan_servis_id')
            ->orderByDesc('total')
            ->first();

        // Paginate hasil
        $laporan = $query->orderBy('tanggal_bayar', 'desc')
            ->paginate(25)
            ->withQueryString();

        return view('adminservis.laporan.index', compact(
            'laporan',
            'totalPendapatan',
            'totalTransaksi',
            'totalBooking',
            'layananTerpopuler'
        ));
    }

    /**
     * Export laporan ke format PDF
     */
    public function exportPdf(Request $request)
    {
        $query = PembayaranServis::with([
            'bookingServis.pelanggan',
            'bookingServis.layananServis',
            'bookingServis.teknisi',
            'bookingServis.rincianServis',
        ]);

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_bayar', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_bayar', '<=', $request->tanggal_sampai);
        }

        if ($request->filled('status')) {
            $query->where('status_pembayaran', $request->status);
        }

        $laporan = $query->orderBy('tanggal_bayar', 'desc')->get();

        $totalPendapatan = $laporan->where('status_pembayaran', 'lunas')->sum('total_biaya');

        $tanggal_dari   = $request->tanggal_dari;
        $tanggal_sampai = $request->tanggal_sampai;
        $status         = $request->status;

        $pdf = Pdf::loadView('adminservis.laporan.pdf', compact(
            'laporan',
            'totalPendapatan',
            'tanggal_dari',
            'tanggal_sampai',
            'status'
        ));

        $pdf->setPaper('a4', 'landscape');

        $fileName = $this->buildFileName($request, 'Laporan_Transaksi_Servis_', 'pdf');

        return $pdf->download($fileName);
    }

    /**
     * Export laporan ke format Excel
     */
    public function exportExcel(Request $request)
    {
        $fileName = $this->buildFileName($request, 'Laporan_Transaksi_Servis_', 'xlsx');

        return Excel::download(new LaporanServisExport($request), $fileName);
    }

    /**
     * Helper: generate nama file export berdasarkan filter tanggal
     */
    private function buildFileName(Request $request, string $prefix, string $ext): string
    {
        if ($request->filled('tanggal_dari') && $request->filled('tanggal_sampai')) {
            return $prefix . $request->tanggal_dari . '_sd_' . $request->tanggal_sampai . '.' . $ext;
        } elseif ($request->filled('tanggal_dari')) {
            return $prefix . 'Sejak_' . $request->tanggal_dari . '.' . $ext;
        } elseif ($request->filled('tanggal_sampai')) {
            return $prefix . 'Hingga_' . $request->tanggal_sampai . '.' . $ext;
        } else {
            return $prefix . date('Y_m_d') . '.' . $ext;
        }
    }
}
