<?php

namespace App\Http\Controllers\KasirFutsal;

use App\Http\Controllers\Controller;
use App\Models\BookingFutsal;
use App\Models\PembayaranFutsal;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $hariIni = Carbon::today();

        // Total booking aktif (hari ini)
        $totalBookingAktif = BookingFutsal::whereDate('start_datetime', $hariIni)
            ->where('status', '!=', 'dibatalkan')
            ->count();

        // Total booking event yang masih aktif (belum selesai/batal)
        $totalEventAktif = BookingFutsal::where('jenis_pembayaran', 'event')
            ->whereIn('status', ['menunggu', 'dikonfirmasi'])
            ->count();

        // Jumlah transaksi selesai
        $jumlahTransaksiSelesai = PembayaranFutsal::whereDate('tgl_bayar', $hariIni)
            ->where('status', PembayaranFutsal::STATUS_VERIFIKASI)
            ->count();

        // Total pemasukan hari ini (berdasarkan tgl_bayar — saat kasir proses lunas)
        $totalPemasukanHariIni = PembayaranFutsal::whereDate('tgl_bayar', $hariIni)
            ->where('status', PembayaranFutsal::STATUS_VERIFIKASI)
            ->sum('jumlah_bayar');

        return view('kasirfutsal.index', compact(
            'totalBookingAktif',
            'totalEventAktif',
            'jumlahTransaksiSelesai',
            'totalPemasukanHariIni'
        ));
    }
}
