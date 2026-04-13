<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BookingFutsal;
use App\Models\BookingAc;
use App\Models\SewaRuko;
use App\Models\PembayaranFutsal;
use App\Models\PembayaranRuko;
use App\Models\PembayaranAc;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class DashboardUserController extends Controller
{
    public function index(): \Illuminate\Contracts\View\View
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (!$user) {
            abort(403, 'Unauthorized');
        }

        $penyewa = $user->penyewa;
        $userId = $user->id;

        return view('user.dashboard', [
            'user' => $user,
            'futsalAktif' => $this->getFutsalAktif($userId),
            'sewaRuko' => $this->getSewaRuko($penyewa),
            'acMendatang' => $this->getAcMendatang($userId),
            'totalTransaksi' => $this->getTotalTransaksi($userId, $penyewa),
            'recentActivities' => $this->getRecentActivities($userId, $penyewa),
        ]);
    }

    private function getFutsalAktif(int $userId): ?BookingFutsal
    {
        return BookingFutsal::where('user_id', $userId)
            ->whereHas('booking', fn($q) => $q->whereIn('status', ['menunggu', 'dikonfirmasi']))
            ->with('booking')
            ->latest('tgl_main')
            ->first();
    }

    private function getSewaRuko($penyewa): ?SewaRuko
    {
        if (!$penyewa) return null;

        return SewaRuko::where('penyewa_id', $penyewa->getKey())
            ->whereIn('status', ['aktif', 'pending'])
            ->with([
                'ruko',
                'pembayaran' => fn($q) => $q->where('status', 'menunggu')->orderBy('tgl_jatuh_tempo')
            ])
            ->first();
    }

    private function getAcMendatang(int $userId): ?BookingAc
    {
        return BookingAc::where('user_id', $userId)
            ->whereIn('status', ['pending', 'proses', 'dikonfirmasi'])
            ->orderBy('tgl_kunjungan')
            ->first();
    }

    private function getTotalTransaksi(int $userId, $penyewa): int
    {
        $futsal = PembayaranFutsal::whereHas('booking', fn($q) => $q->where('user_id', $userId))
            ->whereIn('status', ['Berhasil', 'lunas', 'Lunas'])
            ->count();

        $ruko = $penyewa
            ? PembayaranRuko::whereHas('sewaRuko', fn($q) => $q->where('penyewa_id', $penyewa->getKey()))
                ->where('status', 'lunas')
                ->count()
            : 0;

        $ac = PembayaranAc::whereHas('booking', fn($q) => $q->where('user_id', $userId))
            ->where('status', 'Lunas')
            ->count();

        return $futsal + $ruko + $ac;
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function getRecentActivities(int $userId, $penyewa): Collection
    {
        return $this->getRecentFutsal($userId)
            ->concat($this->getRecentRuko($penyewa))
            ->concat($this->getRecentAc($userId))
            ->sortByDesc('tanggal')
            ->take(10)
            ->values();
    }

    private function getRecentFutsal(int $userId): Collection
    {
        return PembayaranFutsal::with('booking')
            ->whereHas('booking', fn($q) => $q->where('user_id', $userId))
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($item) => $this->formatActivity(
                'Booking Futsal',
                $item->tgl_bayar ?? $item->created_at,
                $item->jumlah_bayar,
                $item->booking?->status ?? $item->status,
                $item
            ));
    }

    private function getRecentRuko($penyewa): Collection
{
    if (!$penyewa) return collect();

    return PembayaranRuko::with('sewaRuko.ruko')
        ->whereHas('sewaRuko', fn($q) => $q->where('penyewa_id', $penyewa->getKey()))
        ->latest()
        ->take(5)
        ->get()
        ->map(fn($item) => $this->formatActivity(
            'Sewa Ruko/Kantin',
            $item->tgl_bayar ?? $item->created_at,
            $item->jumlah_tagihan,
            $item->status,
            $item
        ));
}

    private function getRecentAc(int $userId): Collection
    {
        return PembayaranAc::with('booking')
            ->whereHas('booking', fn($q) => $q->where('user_id', $userId))
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($item) => $this->formatActivity(
                'Servis AC',
                $item->tgl_bayar ?? $item->created_at,
                $item->total_harga,
                $item->booking?->status ?? $item->status,
                $item
            ));
    }

    private function formatActivity(
        string $layanan,
        $tanggal,
        $nominal,
        string $status,
        $raw
    ): array {
        return [
            'layanan' => $layanan,
            'tanggal' => $tanggal,
            'nominal' => $nominal,
            'status' => $status,
            'badge' => $this->getBadgeStatus($status),
            'raw' => $raw
        ];
    }

    private function getBadgeStatus(string $status): string
    {
        $status = strtolower($status);

        return match (true) {
            in_array($status, ['berhasil', 'lunas', 'aktif', 'selesai', 'dikonfirmasi']) => 'success',
            in_array($status, ['menunggu', 'pending', 'proses', 'menunggu pembayaraan', 'sudah bayar dp']) => 'warning',
            in_array($status, ['batal', 'dibatalkan', 'gagal']) => 'danger',
            default => 'secondary',
        };
    }
}