<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
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

        // ── Hitung total transaksi ────────────────────────────────
        $transaksiAC = DB::table('pembayaran_ac')
            ->where('status', 'dibayar')
            ->count();

        $transaksiFutsal = DB::table('pembayaran_futsal')
            ->where('status', 'verifikasi')
            ->count();

        $transaksiRuko = DB::table('pembayaran_ruko')
            ->where('status', 'verifikasi')
            ->count();

        $transaksiServis = DB::table('pembayaran_servis')
            ->where('status_pembayaran', 'lunas')
            ->count();

        $totalTransaksi = $transaksiAC + $transaksiFutsal + $transaksiRuko + $transaksiServis;

        // ── Hitung pendapatan ─────────────────────────────────────
        $pendapatanAC = DB::table('pembayaran_ac')
            ->where('status', 'dibayar')
            ->sum('total_harga');

        $pendapatanFutsal = DB::table('pembayaran_futsal')
            ->where('status', 'verifikasi')
            ->sum('jumlah_bayar');

        $pendapatanRuko = DB::table('pembayaran_ruko')
            ->where('status', 'verifikasi')
            ->sum('jumlah_tagihan');

        $pendapatanServis = DB::table('pembayaran_servis')
            ->where('status_pembayaran', 'lunas')
            ->sum('total_biaya');

        $totalPendapatan = $pendapatanAC + $pendapatanFutsal + $pendapatanRuko + $pendapatanServis;

        // ── Hitung persentase progres pendapatan ──────────────────
        $persenAC     = $totalPendapatan > 0 ? ($pendapatanAC     / $totalPendapatan) * 100 : 0;
        $persenFutsal = $totalPendapatan > 0 ? ($pendapatanFutsal / $totalPendapatan) * 100 : 0;
        $persenRuko   = $totalPendapatan > 0 ? ($pendapatanRuko   / $totalPendapatan) * 100 : 0;
        $persenServis = $totalPendapatan > 0 ? ($pendapatanServis / $totalPendapatan) * 100 : 0;

        $totalBookingPending = Booking::where('status', 'menunggu')->count();

        // ── Transaksi terbaru (union semua sistem + servis) ───────
        $qAC = DB::table('pembayaran_ac')
            ->join('booking_ac', 'pembayaran_ac.booking_id', '=', 'booking_ac.id')
            ->join('users', 'booking_ac.user_id', '=', 'users.id')
            ->select(
                'users.name as nama_user',
                'pembayaran_ac.total_harga as jumlah_bayar',
                'pembayaran_ac.status',
                'pembayaran_ac.tgl_bayar',
                DB::raw("'Cuci AC' as layanan")
            );

        $qFutsal = DB::table('pembayaran_futsal')
            ->join('booking', 'pembayaran_futsal.booking_id', '=', 'booking.id')
            ->join('users', 'booking.user_id', '=', 'users.id')
            ->select(
                'users.name as nama_user',
                'pembayaran_futsal.jumlah_bayar as jumlah_bayar',
                'pembayaran_futsal.status',
                'pembayaran_futsal.tgl_bayar',
                DB::raw("'Futsal' as layanan")
            );

        $qRuko = DB::table('pembayaran_ruko')
            ->join('booking', 'pembayaran_ruko.booking_id', '=', 'booking.id')
            ->join('users', 'booking.user_id', '=', 'users.id')
            ->select(
                'users.name as nama_user',
                'pembayaran_ruko.jumlah_tagihan as jumlah_bayar',
                'pembayaran_ruko.status',
                'pembayaran_ruko.tgl_bayar',
                DB::raw("'Ruko' as layanan")
            );

        $qServis = DB::table('pembayaran_servis')
            ->join('booking_servis', 'pembayaran_servis.booking_servis_id', '=', 'booking_servis.id')
            ->join('users', 'booking_servis.user_id', '=', 'users.id')
            ->select(
                'users.name as nama_user',
                'pembayaran_servis.total_biaya as jumlah_bayar',
                'pembayaran_servis.status_pembayaran as status',
                'pembayaran_servis.tanggal_bayar as tgl_bayar',
                DB::raw("'Servis Kendaraan' as layanan")
            );

        $transaksiTerbaru = DB::query()
            ->fromSub(
                $qAC->unionAll($qFutsal)->unionAll($qRuko)->unionAll($qServis),
                'transaksi'
            )
            ->orderBy('tgl_bayar', 'desc')
            ->limit(5)
            ->get();

        // ── Aktivitas terbaru ─────────────────────────────────────
        $aktivitasAC = DB::table('pembayaran_ac')
            ->join('booking_ac', 'pembayaran_ac.booking_id', '=', 'booking_ac.id')
            ->join('users', 'booking_ac.user_id', '=', 'users.id')
            ->select('users.name as nama', 'pembayaran_ac.created_at', DB::raw("'Transaksi Cuci AC' as aktivitas"));

        $aktivitasFutsal = DB::table('pembayaran_futsal')
            ->join('booking', 'pembayaran_futsal.booking_id', '=', 'booking.id')
            ->join('users', 'booking.user_id', '=', 'users.id')
            ->select('users.name as nama', 'pembayaran_futsal.created_at', DB::raw("'Transaksi Futsal' as aktivitas"));

        $aktivitasRuko = DB::table('pembayaran_ruko')
            ->join('booking', 'pembayaran_ruko.booking_id', '=', 'booking.id')
            ->join('users', 'booking.user_id', '=', 'users.id')
            ->select('users.name as nama', 'pembayaran_ruko.created_at', DB::raw("'Transaksi Sewa Ruko' as aktivitas"));

        $aktivitasServis = DB::table('pembayaran_servis')
            ->join('booking_servis', 'pembayaran_servis.booking_servis_id', '=', 'booking_servis.id')
            ->join('users', 'booking_servis.user_id', '=', 'users.id')
            ->select('users.name as nama', 'pembayaran_servis.created_at', DB::raw("'Transaksi Servis Kendaraan' as aktivitas"));

        $aktivitasUser = DB::table('users')
            ->select('name as nama', 'created_at', DB::raw("'User Baru Terdaftar' as aktivitas"));

        $aktivitas = DB::query()
            ->fromSub(
                $aktivitasAC->unionAll($aktivitasFutsal)->unionAll($aktivitasRuko)
                    ->unionAll($aktivitasServis)->unionAll($aktivitasUser),
                'log'
            )
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact(
            'totalPelanggan',
            'totalTransaksi',
            'totalPendapatan',
            'totalBookingPending',
            'transaksiTerbaru',
            'pendapatanAC',
            'pendapatanFutsal',
            'pendapatanRuko',
            'pendapatanServis',
            'persenAC',
            'persenFutsal',
            'persenRuko',
            'persenServis',
            'aktivitas'
        ));
    }
}
