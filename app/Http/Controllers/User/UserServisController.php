<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BookingServis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserServisController extends Controller
{
    /**
     * Tampilkan Dashboard Servis (Booking Aktif).
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = BookingServis::with(['rincianServis', 'pembayaranServis'])
            ->where('user_id', $user->id)
            ->whereIn('status', ['menunggu', 'diproses']);

        // Filter Status (jika ada input filter di dashboard)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search (Nomor Plat atau Tanggal)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_plat', 'like', "%{$search}%")
                  ->orWhere('tanggal_booking', 'like', "%{$search}%");
            });
        }

        $bookings = $query->latest('tanggal_booking')
            ->paginate(10)
            ->withQueryString();

        return view('user.servis.index', compact('bookings'));
    }

    /**
     * Tampilkan Histori Servis (Semua Booking).
     */
    public function history(Request $request)
    {
        $user = Auth::user();
        
        $query = BookingServis::with(['rincianServis', 'pembayaranServis'])
            ->where('user_id', $user->id);

        // Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_plat', 'like', "%{$search}%")
                  ->orWhere('tanggal_booking', 'like', "%{$search}%");
            });
        }

        $bookings = $query->latest('tanggal_booking')
            ->paginate(15)
            ->withQueryString();

        return view('user.servis.history', compact('bookings'));
    }

    /**
     * Tampilkan detail booking servis.
     */
    public function show($id)
    {
        $booking = BookingServis::with(['rincianServis.produkServis', 'pembayaranServis', 'layananServis', 'teknisi'])
            ->where('user_id', Auth::id()) // Authorization check
            ->findOrFail($id);

        return view('user.servis.show', compact('booking'));
    }
}
