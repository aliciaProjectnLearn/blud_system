<?php

namespace App\Http\Controllers\AdminKantin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PembayaranRuko;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanKantinExport;

class LaporanController extends Controller
{
    /**
     * Tampilkan halaman laporan dan data yang difilter
     */
    public function index(Request $request)
    {
        $query = PembayaranRuko::with([
            'sewaRuko.penyewa.user',
            'sewaRuko.ruko',
            'tipe'
        ]);

        // Filter berdasarkan Range Tanggal
        if ($request->filled('tanggal_dari') && $request->filled('tanggal_sampai')) {
            $query->whereBetween('tgl_bayar', [$request->tanggal_dari . ' 00:00:00', $request->tanggal_sampai . ' 23:59:59']);
        } elseif ($request->filled('tanggal_dari')) {
            $query->where('tgl_bayar', '>=', $request->tanggal_dari . ' 00:00:00');
        } elseif ($request->filled('tanggal_sampai')) {
            $query->where('tgl_bayar', '<=', $request->tanggal_sampai . ' 23:59:59');
        }

        // Mengurutkan dari yang terbaru (tanggal bayar)
        // Gunakan COALESCE atau if null untuk yang belum ada tgl bayar agar diurutkan berdasarkan created_at juga bisa
        $query->orderBy('tgl_bayar', 'desc')->orderBy('created_at', 'desc');

        // OPTIMASI: Hitung total langsung teragregasi di Database, BUKAN di level memori (Collection)
        $totalPendapatan = (clone $query)
            ->where('status', 'lunas')
            ->sum('jumlah_tagihan');

        // Hitung total transaksi (count)
        $totalTransaksi = (clone $query)->count();

        // Gunakan pagination agar data tidak berat, dan bawa query parameternya
        $laporan = $query->paginate(25)->withQueryString();

        return view('adminkantin.laporan.index', compact('laporan', 'totalPendapatan', 'totalTransaksi'));
    }

    /**
     * Export data ke format PDF
     */
    public function exportPdf(Request $request)
    {
        $query = PembayaranRuko::with([
            'sewaRuko.penyewa.user',
            'sewaRuko.ruko',
            'tipe'
        ]);

        if ($request->filled('tanggal_dari') && $request->filled('tanggal_sampai')) {
            $query->whereBetween('tgl_bayar', [$request->tanggal_dari . ' 00:00:00', $request->tanggal_sampai . ' 23:59:59']);
        } elseif ($request->filled('tanggal_dari')) {
            $query->where('tgl_bayar', '>=', $request->tanggal_dari . ' 00:00:00');
        } elseif ($request->filled('tanggal_sampai')) {
            $query->where('tgl_bayar', '<=', $request->tanggal_sampai . ' 23:59:59');
        }

        $query->orderBy('tgl_bayar', 'desc')->orderBy('created_at', 'desc');

        $laporan = $query->get();
        // sum jumlah_tagihan untuk yang lunas
        $totalPendapatan = $laporan->where('status', 'lunas')->sum('jumlah_tagihan');

        // Pemasukan lunas untuk tabel di halaman 2
        $pemasukan = $laporan->where('status', 'lunas');

        // Tambahan: Hitung Pengeluaran untuk PDF
        $queryPengeluaran = \App\Models\PengeluaranKantin::query();
        if ($request->filled('tanggal_dari') && $request->filled('tanggal_sampai')) {
            $queryPengeluaran->whereBetween('tanggal', [$request->tanggal_dari, $request->tanggal_sampai]);
        } elseif ($request->filled('tanggal_dari')) {
            $queryPengeluaran->where('tanggal', '>=', $request->tanggal_dari);
        } elseif ($request->filled('tanggal_sampai')) {
            $queryPengeluaran->where('tanggal', '<=', $request->tanggal_sampai);
        }
        
        $pengeluaran = $queryPengeluaran->orderBy('tanggal', 'asc')->get();
        $totalPengeluaran = $pengeluaran->sum('nominal');
        $saldoAkhir = $totalPendapatan - $totalPengeluaran;

        $pdf = Pdf::loadView('adminkantin.laporan.pdf', compact(
            'laporan', 
            'pemasukan',
            'pengeluaran', 
            'totalPendapatan', 
            'totalPengeluaran', 
            'saldoAkhir'
        ));
        
        $fileName = 'Laporan_Transaksi_Kantin_';
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
        $fileName = 'Laporan_Transaksi_Kantin_';
        if ($request->filled('tanggal_dari') && $request->filled('tanggal_sampai')) {
            $fileName .= $request->tanggal_dari . '_sd_' . $request->tanggal_sampai;
        } elseif ($request->filled('tanggal_dari')) {
            $fileName .= 'Sejak_' . $request->tanggal_dari;
        } elseif ($request->filled('tanggal_sampai')) {
            $fileName .= 'Hingga_' . $request->tanggal_sampai;
        } else {
            $fileName .= date('Y_m_d');
        }

        return Excel::download(new LaporanKantinExport($request), $fileName . '.xlsx');
    }
}
