<?php

namespace App\Http\Controllers\AdminKantin;

use App\Http\Controllers\Controller;
use App\Models\Ruko;
use App\Models\SewaRuko;
use App\Models\PembayaranRuko;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Total pendapatan (pembayaran yang sudah verifikasi)
        $totalPendapatan = PembayaranRuko::where('status', 'verifikasi')->sum('jumlah_tagihan');

        // Total penyewa aktif
        $totalPenyewa = SewaRuko::where('status', 'disetujui')->count();

        // Total unit terisi & keseluruhan
        $totalUnitTerisi = Ruko::where('status_unit', 'terisi')->count();
        $totalUnit       = Ruko::count();

        // Status pembayaran per termin
        $statusTermin = [
            'termin1_menunggu'   => PembayaranRuko::where('termin', 1)->where('status', 'menunggu')->count(),
            'termin1_verifikasi' => PembayaranRuko::where('termin', 1)->where('status', 'verifikasi')->count(),
            'termin1_dibatalkan' => PembayaranRuko::where('termin', 1)->where('status', 'dibatalkan')->count(),
            'termin2_menunggu'   => PembayaranRuko::where('termin', 2)->where('status', 'menunggu')->count(),
            'termin2_verifikasi' => PembayaranRuko::where('termin', 2)->where('status', 'verifikasi')->count(),
            'termin2_dibatalkan' => PembayaranRuko::where('termin', 2)->where('status', 'dibatalkan')->count(),
        ];

        // Status pembayaran keseluruhan (untuk progress bar)
        $statusVerifikasi = PembayaranRuko::where('status', 'verifikasi')->count();
        $statusMenunggu   = PembayaranRuko::where('status', 'menunggu')->count();
        $statusDibatalkan = PembayaranRuko::where('status', 'dibatalkan')->count();
        $totalTransaksi   = PembayaranRuko::count();


        // Transaksi terbaru
        $transaksiTerbaru = PembayaranRuko::with(['sewaRuko.penyewa.user', 'sewaRuko.ruko.kategori'])
            ->latest()
            ->take(5)
            ->get();

        return view('adminkantin.index', compact(
            'totalPendapatan',
            'totalPenyewa',
            'totalUnitTerisi',
            'totalUnit',
            'statusTermin',
            'statusVerifikasi',
            'statusMenunggu',
            'statusDibatalkan',
            'totalTransaksi',
            'transaksiTerbaru',
        ));
    }
}
