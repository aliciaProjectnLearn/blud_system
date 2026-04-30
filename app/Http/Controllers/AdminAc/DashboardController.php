<?php

namespace App\Http\Controllers\AdminAc;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();
        
        $filterBulan = $request->get('bulan_filter', $today->month);

        // ── Total Transaksi AC ──────────────────────────────
        $totalTransaksi = DB::table('pembayaran_ac')->count();

        // ── Total Pendapatan AC (status dibayar) ─────────
        $totalPendapatan = DB::table('pembayaran_ac')
            ->where('status', 'dibayar')
            ->sum('total_harga');

        // ── Transaksi Hari Ini ──────────────────────────────
        $transaksiHariIni = DB::table('pembayaran_ac')
            ->whereDate('tgl_bayar', $today)
            ->count();

        // ── Total Layanan AC ────────────────────────────────
        $totalLayanan = DB::table('layanan_ac')->count();

        // ── Total Produk/Alat ───────────────────────────────
        $totalProduk  = DB::table('produks')->count();
        $totalStok    = DB::table('produks')->sum('stok');

        // ── Total Teknisi ───────────────────────────────────
        $totalTeknisi = DB::table('users')
            ->join('roles_users', 'users.id', '=', 'roles_users.user_id')
            ->join('roles', 'roles.id', '=', 'roles_users.role_id')
            ->where('roles.nama', 'Teknisi')
            ->count();

        // ── Status Pembayaran ───────────────────────────────
        $statusMenunggu   = DB::table('pembayaran_ac')->where('status', 'pending')->count();
        $statusVerifikasi = DB::table('pembayaran_ac')->where('status', 'dibayar')->count();

        // ── Status Booking AC ───────────────────────────────
        $bookingMenunggu  = DB::table('booking_ac')->where('status', 'menunggu')->count();
        $bookingProses    = DB::table('booking_ac')->where('status', 'proses')->count();
        $bookingSelesai   = DB::table('booking_ac')->where('status', 'selesai')->count();

        // ── Transaksi Terbaru ───────────────────────────────
        $transaksiTerbaru = DB::table('pembayaran_ac')
            ->join('booking_ac', 'pembayaran_ac.booking_id', '=', 'booking_ac.id')
            ->join('users', 'booking_ac.user_id', '=', 'users.id')
            ->join('layanan_ac', 'booking_ac.layanan_id', '=', 'layanan_ac.id')
            ->select(
                'users.name as nama_user',
                'layanan_ac.nama as nama_layanan',
                'pembayaran_ac.total_harga',
                'pembayaran_ac.status',
                'pembayaran_ac.tgl_bayar'
            )
            ->orderBy('pembayaran_ac.tgl_bayar', 'desc')
            ->limit(5)
            ->get();

        // ── Jadwal Kunjungan Hari Ini ───────────────────────
        $jadwalHariIni = DB::table('booking_ac')
            ->join('users', 'booking_ac.user_id', '=', 'users.id')
            ->join('layanan_ac', 'booking_ac.layanan_id', '=', 'layanan_ac.id')
            ->select(
                'users.name as nama_user',
                'layanan_ac.nama as nama_layanan',
                'booking_ac.tgl_kunjungan',
                'booking_ac.alamat',
                'booking_ac.merek_ac',
                'booking_ac.status'
            )
            ->whereDate('booking_ac.tgl_kunjungan', $today)
            ->orderBy('booking_ac.tgl_kunjungan', 'asc')
            ->get();

        // ── Pendapatan Per Bulan ────────────────────────────
        $pendapatanPerBulan = DB::table('pembayaran_ac')
            ->where('status', 'dibayar')
            ->whereYear('tgl_bayar', $today->year)
            ->select(
                DB::raw('MONTH(tgl_bayar) as bulan'),
                DB::raw('SUM(total_harga) as total')
            )
            ->groupBy(DB::raw('MONTH(tgl_bayar)'))
            ->orderBy('bulan')
            ->get();

        $labelBulan = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'];
        $dataPendapatan = array_fill(0, 12, 0);
        foreach ($pendapatanPerBulan as $p) {
            $dataPendapatan[$p->bulan - 1] = $p->total;
        }

        $totalTeknisi = DB::table('users')
            ->join('roles_users', 'users.id', '=', 'roles_users.user_id')
            ->join('roles', 'roles.id', '=', 'roles_users.role_id')
            ->where('roles.nama', 'Teknisi')
            ->count();

        return view('adminac.index', compact(
            'totalTransaksi',
            'totalPendapatan',
            'transaksiHariIni',
            'statusMenunggu',
            'statusVerifikasi',
            'bookingMenunggu',
            'bookingProses',
            'bookingSelesai',
            'transaksiTerbaru',
            'jadwalHariIni',
            'labelBulan',
            'dataPendapatan',
            'totalLayanan',
            'totalProduk',
            'totalStok',
            'totalTeknisi'
        ));
    }
}
