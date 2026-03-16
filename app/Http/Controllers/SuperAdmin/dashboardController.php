<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{


public function index()
{
    $totalPelanggan = DB::table('users')
        ->join('roles_users', 'users.id', '=', 'roles_users.user_id')
        ->join('roles', 'roles.id', '=', 'roles_users.role_id')
        ->where('roles.nama', 'pelanggan')
        ->count();

        $transaksiAC = DB::table('pembayaran_ac')
        ->where('status', 'verifikasi')
        ->count();

    $transaksiFutsal = DB::table('pembayaran_futsal')
        ->where('status', 'verifikasi')
        ->count();

    $transaksiRuko = DB::table('pembayaran_ruko')
        ->where('status', 'verifikasi')
        ->count();

    $totalTransaksi = $transaksiAC + $transaksiFutsal + $transaksiRuko;

    $pendapatanAC = DB::table('pembayaran_ac')
        ->where('status','verifikasi')
        ->sum('total_biaya');

    $pendapatanFutsal = DB::table('pembayaran_futsal')
        ->where('status','verifikasi')
        ->sum('jumlah_bayar');

    $pendapatanRuko = DB::table('pembayaran_ruko')
        ->where('status','verifikasi')
        ->sum('jumlah_tagihan');

    $totalPendapatan = $pendapatanAC + $pendapatanFutsal + $pendapatanRuko;

    $totalPendapatan = $pendapatanAC + $pendapatanFutsal + $pendapatanRuko;

        $persenAC = $totalPendapatan > 0 ? ($pendapatanAC / $totalPendapatan) * 100 : 0;
        $persenFutsal = $totalPendapatan > 0 ? ($pendapatanFutsal / $totalPendapatan) * 100 : 0;
        $persenRuko = $totalPendapatan > 0 ? ($pendapatanRuko / $totalPendapatan) * 100 : 0;

    $totalBookingPending = Booking::where('status', 'menunggu')->count();

$transaksiAC = DB::table('pembayaran_ac')
    ->join('booking', 'pembayaran_ac.booking_id', '=', 'booking.id')
    ->join('users', 'booking.user_id', '=', 'users.id')
    ->select(
        'users.name as nama_user',
        'pembayaran_ac.total_biaya as jumlah_bayar',
        'pembayaran_ac.status',
        'pembayaran_ac.tgl_bayar',
        DB::raw("'Cuci AC' as layanan")
    );

$transaksiFutsal = DB::table('pembayaran_futsal')
    ->join('booking', 'pembayaran_futsal.booking_id', '=', 'booking.id')
    ->join('users', 'booking.user_id', '=', 'users.id')
    ->select(
        'users.name as nama_user',
        'pembayaran_futsal.jumlah_bayar as jumlah_bayar',
        'pembayaran_futsal.status',
        'pembayaran_futsal.tgl_bayar',
        DB::raw("'Futsal' as layanan")
    );

$transaksiRuko = DB::table('pembayaran_ruko')
    ->join('booking', 'pembayaran_ruko.booking_id', '=', 'booking.id')
    ->join('users', 'booking.user_id', '=', 'users.id')
    ->select(
        'users.name as nama_user',
        'pembayaran_ruko.jumlah_tagihan as jumlah_bayar',
        'pembayaran_ruko.status',
        'pembayaran_ruko.tgl_bayar',
        DB::raw("'Ruko' as layanan")
    );

$transaksiTerbaru = $transaksiAC
    ->unionAll($transaksiFutsal)
    ->unionAll($transaksiRuko);

$transaksiTerbaru = DB::query()
    ->fromSub($transaksiTerbaru, 'transaksi')
    ->orderBy('tgl_bayar', 'desc')
    ->limit(5)
    ->get();

$aktivitasAC = DB::table('pembayaran_ac')
    ->join('booking','pembayaran_ac.booking_id','=','booking.id')
    ->join('users','booking.user_id','=','users.id')
    ->select(
        'users.name as nama',
        'pembayaran_ac.created_at',
        DB::raw("'Transaksi Cuci AC' as aktivitas")
    );

$aktivitasFutsal = DB::table('pembayaran_futsal')
    ->join('booking','pembayaran_futsal.booking_id','=','booking.id')
    ->join('users','booking.user_id','=','users.id')
    ->select(
        'users.name as nama',
        'pembayaran_futsal.created_at',
        DB::raw("'Transaksi Futsal' as aktivitas")
    );

$aktivitasRuko = DB::table('pembayaran_ruko')
    ->join('booking','pembayaran_ruko.booking_id','=','booking.id')
    ->join('users','booking.user_id','=','users.id')
    ->select(
        'users.name as nama',
        'pembayaran_ruko.created_at',
        DB::raw("'Transaksi Sewa Ruko' as aktivitas")
    );

$aktivitasUser = DB::table('users')
    ->select(
        'name as nama',
        'created_at',
        DB::raw("'User Baru Terdaftar' as aktivitas")
    );

$aktivitas = $aktivitasAC
    ->unionAll($aktivitasFutsal)
    ->unionAll($aktivitasRuko)
    ->unionAll($aktivitasUser);

$aktivitas = DB::query()
    ->fromSub($aktivitas,'log')
    ->orderBy('created_at','desc')
    ->limit(5)
    ->get();

    return view('dashboard.index', compact('totalPelanggan', 'totalTransaksi', 'totalPendapatan', 'totalBookingPending', 'transaksiTerbaru',
    'pendapatanAC', 'pendapatanFutsal', 'pendapatanRuko', 'persenAC', 'persenFutsal', 'persenRuko', 'aktivitas'));
}
}

