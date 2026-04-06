<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BookingFutsal;
use App\Models\BookingAc;
use App\Models\SewaRuko;
use App\Models\PembayaranFutsal;
use App\Models\PembayaranRuko;
use App\Models\PembayaranAc;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardUserController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1. Status Booking Futsal Aktif
        $futsalAktif = BookingFutsal::where('user_id', $user->id)
            ->whereHas('booking', function($q) {
                $q->whereIn('status', ['menunggu', 'dikonfirmasi']);
            })
            ->with('booking')
            ->orderBy('tgl_main', 'desc')
            ->first();

        // 2. Status Sewa Kantin/Ruko (Masa aktif/Jatuh tempo)
        // Ambil sewa yang masih aktif atau paling mendekati selesai
        $penyewa = $user->penyewa;
        $sewaRuko = null;
        if ($penyewa) {
            $sewaRuko = SewaRuko::where('penyewa_id', $penyewa->id)
                ->where('status', 'aktif')
                ->with(['ruko', 'pembayaran' => function($q) {
                    $q->where('status', 'menunggu')->orderBy('tgl_jatuh_tempo', 'asc');
                }])
                ->first();
        }

        // 3. Jadwal Servis AC Mendatang
        $acMendatang = BookingAc::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'proses', 'dikonfirmasi'])
            ->orderBy('tgl_kunjungan', 'asc')
            ->first();

        // 4. Total Riwayat Transaksi (Gabungan dari semua layanan)
        $countFutsal = PembayaranFutsal::whereHas('booking', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->whereIn('status', ['Berhasil', 'lunas', 'Lunas'])->count();
        
        $countRuko = 0;
        if ($penyewa) {
            $countRuko = PembayaranRuko::whereHas('sewaRuko', function($q) use ($penyewa) {
                $q->where('penyewa_id', $penyewa->id);
            })->where('status', 'lunas')->count();
        }

        $countAc = PembayaranAc::whereHas('booking', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->where('status', 'Lunas')->count();

        $totalTransaksi = $countFutsal + $countRuko + $countAc;

        // Recent Activities Table (Combined histori transaksi terakhir)
        $recentFutsal = PembayaranFutsal::with('booking')
            ->whereHas('booking', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->latest()
            ->take(5)
            ->get()
            ->map(function($item) {
                return [
                    'layanan' => 'Booking Futsal',
                    'tanggal' => $item->tgl_bayar ?? $item->created_at,
                    'nominal' => $item->jumlah_bayar,
                    'status' => $item->booking->status ?? $item->status,
                    'badge' => $this->getBadgeStatus($item->booking->status ?? $item->status),
                    'raw' => $item
                ];
            });

        $recentRuko = collect();
        if ($penyewa) {
            $recentRuko = PembayaranRuko::with('booking')
                ->whereHas('booking', function($q) use ($penyewa) {
                    $q->where('penyewa_id', $penyewa->id);
                })
                ->latest()
                ->take(5)
                ->get()
                ->map(function($item) {
                    return [
                        'layanan' => 'Sewa Ruko/Kantin',
                        'tanggal' => $item->tgl_bayar ?? $item->created_at,
                        'nominal' => $item->jumlah_tagihan,
                    'status' => $item->booking->status ?? $item->status,
                    'badge' => $this->getBadgeStatus($item->booking->status ?? $item->status),
                    'raw' => $item
                ];
                });
        }

        $recentAc = PembayaranAc::with('booking')
            ->whereHas('booking', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->latest()
            ->take(5)
            ->get()
            ->map(function($item) {
                return [
                    'layanan' => 'Servis AC',
                    'tanggal' => $item->tgl_bayar ?? $item->created_at,
                    'nominal' => $item->total_harga,
                    'status' => $item->booking->status ?? $item->status,
                    'badge' => $this->getBadgeStatus($item->booking->status ?? $item->status),
                    'raw' => $item
                ];
            });

        $recentActivities = $recentFutsal->concat($recentRuko)->concat($recentAc)
            ->sortByDesc('tanggal')
            ->take(10);

        return view('user.dashboard', compact(
            'user', 
            'futsalAktif', 
            'sewaRuko', 
            'acMendatang', 
            'totalTransaksi',
            'recentActivities'
        ));
    }

    private function getBadgeStatus($status)
    {
        $status = strtolower($status);
        if (in_array($status, ['berhasil', 'lunas', 'aktif', 'selesai', 'dikonfirmasi'])) {
            return 'success';
        } elseif (in_array($status, ['menunggu', 'pending', 'proses', 'menunggu pembayaraan', 'sudah bayar dp'])) {
            return 'warning';
        } elseif (in_array($status, ['batal', 'dibatalkan', 'gagal'])) {
            return 'danger';
        }
        return 'secondary';
    }
}
