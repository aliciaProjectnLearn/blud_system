<?php

namespace App\Http\Controllers\KasirServis;

use App\Http\Controllers\Controller;
use App\Models\BookingServis;
use App\Models\PembayaranServis;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $hariIni = Carbon::today();

        // --- Data Ringkasan (Summary Cards) ---
        $totalBookingHariIni = BookingServis::whereDate('tanggal_booking', $hariIni)->count();
        $totalTransaksiHariIni = PembayaranServis::whereDate('tanggal_bayar', $hariIni)
            ->where('status_pembayaran', 'lunas')
            ->count();

        $totalPemasukanHariIni = PembayaranServis::whereDate('tanggal_bayar', $hariIni)
            ->where('status_pembayaran', 'lunas')
            ->sum('total_biaya');

        // --- Data Grafik (7 Hari Terakhir) ---
        $grafikBooking = collect();
        $grafikPemasukan = collect();

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $label = $date->translatedFormat('d M');

            // Hitung booking per hari
            $countBooking = BookingServis::whereDate('tanggal_booking', $date->toDateString())->count();
            $grafikBooking->push(['label' => $label, 'total' => $countBooking]);

            // Hitung pemasukan per hari (hanya yang lunas)
            $sumPemasukan = PembayaranServis::whereDate('tanggal_bayar', $date->toDateString())
                ->where('status_pembayaran', 'lunas')
                ->sum('total_biaya');
            $grafikPemasukan->push(['label' => $label, 'total' => $sumPemasukan]);
        }

        return view('kasirservis.index', compact(
            'totalBookingHariIni',
            'totalTransaksiHariIni',
            'totalPemasukanHariIni',
            'grafikBooking',
            'grafikPemasukan'
        ));
    }
}
