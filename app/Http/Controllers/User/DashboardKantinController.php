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

        // Ambil penyewa
        $penyewa = Penyewa::where('user_id', $user->id)->first();

        if (!$penyewa) {
            return response()->json([
                'message' => 'User belum terdaftar sebagai penyewa'
            ], 404);
        }

        // Base query
        $baseQuery = SewaRuko::where('penyewa_id', $penyewa->id);

        // Statistik
        $total = (clone $baseQuery)->count();
        $aktif = (clone $baseQuery)->where('status', 'aktif')->count();
        $selesai = (clone $baseQuery)->where('status', 'selesai')->count();
        $dibatalkan = (clone $baseQuery)->where('status', 'dibatalkan')->count();

        // Transaksi terbaru
        $latest = (clone $baseQuery)
            ->latest()
            ->take(5)
            ->get();

        // Tagihan (belum bayar)
        $tagihan = PembayaranRuko::whereHas('sewaRuko', function ($q) use ($penyewa) {
                $q->where('penyewa_id', $penyewa->id);
            })
            ->where('status', 'belum_bayar')
            ->get();

        return response()->json([
            'summary' => [
                'total' => $total,
                'aktif' => $aktif,
                'selesai' => $selesai,
                'dibatalkan' => $dibatalkan,
                'tagihan' => $tagihan->count(),
            ],
            'latest' => $latest,
            'tagihan' => $tagihan,
        ]);
    }
}