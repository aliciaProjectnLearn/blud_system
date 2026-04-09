<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Penyewa;
use App\Models\SewaRuko;
use App\Models\PembayaranRuko;

class DashboardKantinController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Ambil penyewa (bisa null)
        $penyewa = Penyewa::where('user_id', $user->id)->first();

        // Inisialisasi default
        $totalSewa = 0;
        $totalDikonfirmasi = 0;
        $totalSelesai = 0;
        $tagihanMendatang = null;
        $sewaAktif = null;
        $labelGrafik = [];
        $dataGrafik = [];
        $transaksiTerbaru = collect();

        if ($penyewa) {
            // Base query
            $baseQuery = SewaRuko::where('penyewa_id', $penyewa->id);

            // Statistik
            $totalSewa = (clone $baseQuery)->count();

            // Pembayaran: hitung berdasarkan status
            $totalDikonfirmasi = PembayaranRuko::whereHas('sewaRuko', function ($q) use ($penyewa) {
                $q->where('penyewa_id', $penyewa->id);
            })->where('status', 'verifikasi')->count();

            $totalSelesai = PembayaranRuko::whereHas('sewaRuko', function ($q) use ($penyewa) {
                $q->where('penyewa_id', $penyewa->id);
            })->where('status', 'lunas')->count();

            // Transaksi terbaru (pembayaran)
            $transaksiTerbaru = PembayaranRuko::whereHas('sewaRuko', function ($q) use ($penyewa) {
                $q->where('penyewa_id', $penyewa->id);
            })->latest()->take(5)->get();

            // Tagihan (belum bayar / menunggu)
            $tagihanMendatang = PembayaranRuko::whereHas('sewaRuko', function ($q) use ($penyewa) {
                $q->where('penyewa_id', $penyewa->id);
            })->where('status', 'menunggu')->orderBy('tgl_jatuh_tempo')->first();

            // Ambil sewa aktif beserta relasi pembayaran dan ruko
            $sewaAktif = SewaRuko::with('pembayaran', 'ruko')
                ->where('penyewa_id', $penyewa->id)
                ->where('status', 'aktif')
                ->first();

            // Siapkan data grafik (total tagihan per bulan tahun ini)
            $year = now()->year;
            $labelGrafik = [];
            $dataGrafik = [];

            for ($m = 1; $m <= 12; $m++) {
                $labelGrafik[] = \Carbon\Carbon::createFromDate($year, $m, 1)->format('M');
                $sum = PembayaranRuko::whereHas('sewaRuko', function ($q) use ($penyewa) {
                    $q->where('penyewa_id', $penyewa->id);
                })->whereYear('tgl_jatuh_tempo', $year)->whereMonth('tgl_jatuh_tempo', $m)->sum('jumlah_tagihan');

                $dataGrafik[] = (int) $sum;
            }
        }

        // Kembalikan view dengan semua variabel yang dipakai blade
        return view('user.kantin.dashboard', compact(
            'penyewa',
            'totalSewa',
            'totalDikonfirmasi',
            'totalSelesai',
            'tagihanMendatang',
            'sewaAktif',
            'labelGrafik',
            'dataGrafik',
            'transaksiTerbaru'
        ));
    }
}
