<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingFutsal;
use App\Models\BookingAc;
use App\Models\BookingServis;
use App\Models\SewaRuko;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TokenAccessController extends Controller
{
    /**
     * Tampilkan detail booking berdasarkan token.
     */
    public function show($token)
    {
        $booking = $this->findBookingByToken($token);

        if (!$booking) {
            return abort(404, 'Token tidak valid atau booking tidak ditemukan.');
        }

        $type = $this->getBookingType($booking);
        $user = $booking->user ?? $booking->penyewaUser ?? null; // Adjust based on model relations

        return view('user.token.detail', compact('booking', 'type', 'token', 'user'));
    }

    /**
     * Tampilkan riwayat semua booking berdasarkan nomor HP dari token yang diberikan.
     */
    public function riwayat($token)
    {
        $currentBooking = $this->findBookingByToken($token);

        if (!$currentBooking) {
            return abort(404, 'Token tidak valid.');
        }

        $user = $currentBooking->user ?? $currentBooking->penyewaUser; // Logic to get user
        
        if (!$user) {
            return back()->with('error', 'Data pengguna tidak ditemukan.');
        }

        // Get all bookings for this user across all services
        $bookings = [
            'futsal' => BookingFutsal::where('user_id', $user->id)->with('booking')->latest()->get(),
            'ac'     => BookingAc::where('user_id', $user->id)->latest()->get(),
            'servis' => BookingServis::where('user_id', $user->id)->latest()->get(),
            'kantin' => SewaRuko::where('penyewa_id', $user->id)->with('booking')->latest()->get(),
        ];

        return view('user.token.riwayat', compact('bookings', 'user', 'token'));
    }

    /**
     * Tampilkan halaman konfirmasi pembatalan.
     */
    public function batalkan($token)
    {
        $booking = $this->findBookingByToken($token);

        if (!$booking) {
            return abort(404, 'Token tidak valid.');
        }

        // Validasi status & waktu pembatalan
        $canCancel = $this->checkCanCancel($booking);

        if (!$canCancel['allowed']) {
            return redirect()->route('user.token.show', $token)->with('error', $canCancel['message']);
        }

        return view('user.token.batalkan', compact('booking', 'token'));
    }

    /**
     * Proses pembatalan booking.
     */
    public function prosesBatalkan(Request $request, $token)
    {
        $booking = $this->findBookingByToken($token);

        if (!$booking) {
            return abort(404, 'Token tidak valid.');
        }

        $canCancel = $this->checkCanCancel($booking);
        if (!$canCancel['allowed']) {
            return redirect()->route('user.token.show', $token)->with('error', $canCancel['message']);
        }

        // Update status
        if (isset($booking->status)) {
            if ($booking instanceof \App\Models\BookingServis) {
                $booking->status = 'batal';
            } else {
                $booking->status = 'dibatalkan';
            }
            $booking->save();
        }
        
        // If it has a parent booking table record
        if (isset($booking->booking_id) && $booking->booking) {
            $booking->booking->status = 'dibatalkan';
            $booking->booking->save();
        }

        return redirect()->route('user.token.show', $token)->with('success', 'Booking berhasil dibatalkan.');
    }

    /**
     * Helper to find booking across tables by token.
     */
    private function findBookingByToken($token)
    {
        return BookingFutsal::where('access_token', $token)->first()
            ?? BookingAc::where('access_token', $token)->first()
            ?? BookingServis::where('access_token', $token)->first()
            ?? SewaRuko::where('access_token', $token)->first()
            ?? Booking::where('access_token', $token)->first();
    }

    private function getBookingType($booking)
    {
        if ($booking instanceof BookingFutsal) return 'futsal';
        if ($booking instanceof BookingAc) return 'ac';
        if ($booking instanceof BookingServis) return 'servis';
        if ($booking instanceof SewaRuko) return 'kantin';
        return 'umum';
    }

    private function checkCanCancel($booking)
    {
        $status = strtolower($booking->status ?? ($booking->booking->status ?? ''));
        
        if (in_array($status, ['selesai', 'dibatalkan', 'proses', 'diproses'])) {
            return ['allowed' => false, 'message' => 'Booking dengan status ' . $status . ' tidak dapat dibatalkan.'];
        }

        // Add time-based validation if needed (e.g., max 24h before)
        // For now, let's allow if status is 'menunggu' or 'dikonfirmasi'
        
        return ['allowed' => true, 'message' => ''];
    }
}
