<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ── Total pelanggan ───────────────────────────────────────
        $totalPelanggan = (int) DB::table('users')
            ->join('roles_users', 'users.id', '=', 'roles_users.user_id')
            ->join('roles', 'roles.id', '=', 'roles_users.role_id')
            ->whereIn('roles.nama', ['pelanggan', 'Pelanggan', 'user', 'User'])
            ->count();

        // ── Total transaksi per sistem ────────────────────────────
        $transaksiAC     = (int) DB::table('pembayaran_ac')->where('status', 'dibayar')->count();
        $transaksiFutsal = (int) DB::table('pembayaran_futsal')->where('status', 'verifikasi')->count();
        $transaksiRuko   = (int) DB::table('pembayaran_ruko')->where('status', 'verifikasi')->count();
        $transaksiServis = (int) DB::table('pembayaran_servis')->where('status_pembayaran', 'lunas')->count();
        $totalTransaksi  = $transaksiAC + $transaksiFutsal + $transaksiRuko + $transaksiServis;

        // ── Pendapatan per sistem ─────────────────────────────────
        $pendapatanAC     = (float) DB::table('pembayaran_ac')->where('status', 'dibayar')->sum('total_harga');
        $pendapatanFutsal = (float) DB::table('pembayaran_futsal')->where('status', 'verifikasi')->sum('jumlah_bayar');
        $pendapatanRuko   = (float) DB::table('pembayaran_ruko')->where('status', 'verifikasi')->sum('jumlah_tagihan');
        $pendapatanServis = (float) DB::table('pembayaran_servis')->where('status_pembayaran', 'lunas')->sum('total_biaya');
        $totalPendapatan  = $pendapatanAC + $pendapatanFutsal + $pendapatanRuko + $pendapatanServis;

        // ── Persentase kontribusi pendapatan (aman jika total = 0) ─
        $persenAC     = $totalPendapatan > 0 ? round(($pendapatanAC     / $totalPendapatan) * 100, 1) : 0;
        $persenFutsal = $totalPendapatan > 0 ? round(($pendapatanFutsal / $totalPendapatan) * 100, 1) : 0;
        $persenRuko   = $totalPendapatan > 0 ? round(($pendapatanRuko   / $totalPendapatan) * 100, 1) : 0;
        $persenServis = $totalPendapatan > 0 ? round(($pendapatanServis / $totalPendapatan) * 100, 1) : 0;

        // ── Booking pending (fallback 0 jika tabel kosong) ────────
        $totalBookingPending = 0;
        try {
            $pendingAc     = DB::table('booking')->where('status', 'menunggu')->count();
            $pendingServis = DB::table('booking_servis')->where('status', 'menunggu')->count();
            $pendingFutsal = DB::table('booking_futsal')->where('status', 'menunggu')->count();
            $totalBookingPending = $pendingAc + $pendingServis + $pendingFutsal;
        } catch (\Throwable $e) {
            $totalBookingPending = 0;
        }

        // ── 5 Transaksi terbaru (gabungan semua sistem) ───────────
        $transaksiTerbaru = collect();
        try {
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
                ->join('booking_futsal', 'pembayaran_futsal.booking_futsal_id', '=', 'booking_futsal.id')
                ->join('users', 'booking_futsal.user_id', '=', 'users.id')
                ->select(
                    'users.name as nama_user',
                    'pembayaran_futsal.jumlah_bayar as jumlah_bayar',
                    'pembayaran_futsal.status',
                    'pembayaran_futsal.tgl_bayar',
                    DB::raw("'Futsal' as layanan")
                );

            $qRuko = DB::table('pembayaran_ruko')
                ->join('sewa_ruko', 'pembayaran_ruko.sewa_ruko_id', '=', 'sewa_ruko.id')
                ->join('penyewa', 'sewa_ruko.penyewa_id', '=', 'penyewa.id')
                ->join('users', 'penyewa.user_id', '=', 'users.id')
                ->select(
                    'users.name as nama_user',
                    'pembayaran_ruko.jumlah_tagihan as jumlah_bayar',
                    'pembayaran_ruko.status',
                    'pembayaran_ruko.tgl_bayar',
                    DB::raw("'Ruko/Kantin' as layanan")
                );

            $qServis = DB::table('pembayaran_servis')
                ->join('booking_servis', 'pembayaran_servis.booking_servis_id', '=', 'booking_servis.id')
                ->leftJoin('users', 'booking_servis.user_id', '=', 'users.id')
                ->select(
                    DB::raw("COALESCE(booking_servis.nama_pemesan, users.name, 'Guest') as nama_user"),
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
        } catch (\Throwable $e) {
            // Jika ada tabel yang belum ada, kembalikan collection kosong
            $transaksiTerbaru = collect();
        }

        // ── 5 Aktivitas terbaru ───────────────────────────────────
        $aktivitas = collect();
        try {
            $aktivitasAC = DB::table('pembayaran_ac')
                ->join('booking_ac', 'pembayaran_ac.booking_id', '=', 'booking_ac.id')
                ->join('users', 'booking_ac.user_id', '=', 'users.id')
                ->select('users.name as nama', 'pembayaran_ac.created_at', DB::raw("'Transaksi Cuci AC' as aktivitas"));

            $aktivitasFutsal = DB::table('pembayaran_futsal')
                ->join('booking_futsal', 'pembayaran_futsal.booking_futsal_id', '=', 'booking_futsal.id')
                ->join('users', 'booking_futsal.user_id', '=', 'users.id')
                ->select('users.name as nama', 'pembayaran_futsal.created_at', DB::raw("'Transaksi Futsal' as aktivitas"));

            $aktivitasRuko = DB::table('pembayaran_ruko')
                ->join('sewa_ruko', 'pembayaran_ruko.sewa_ruko_id', '=', 'sewa_ruko.id')
                ->join('penyewa', 'sewa_ruko.penyewa_id', '=', 'penyewa.id')
                ->join('users', 'penyewa.user_id', '=', 'users.id')
                ->select('users.name as nama', 'pembayaran_ruko.created_at', DB::raw("'Transaksi Sewa Ruko' as aktivitas"));

            $aktivitasServis = DB::table('pembayaran_servis')
                ->join('booking_servis', 'pembayaran_servis.booking_servis_id', '=', 'booking_servis.id')
                ->leftJoin('users', 'booking_servis.user_id', '=', 'users.id')
                ->select(
                    DB::raw("COALESCE(booking_servis.nama_pemesan, users.name, 'Guest') as nama"),
                    'pembayaran_servis.created_at',
                    DB::raw("'Transaksi Servis Kendaraan' as aktivitas")
                );

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
        } catch (\Throwable $e) {
            $aktivitas = collect();
        }

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
