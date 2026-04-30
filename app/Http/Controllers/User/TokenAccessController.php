<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BookingFutsal;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TokenAccessController extends Controller
{
    public function show($token)
    {
        $bookingFutsal = BookingFutsal::with(['booking', 'lapangan'])
            ->where('access_token', $token)->firstOrFail();

        return view('user.token.detail', compact('bookingFutsal', 'token'));
    }

    public function riwayat($token)
    {
        $bookingFutsal = BookingFutsal::where('access_token', $token)->firstOrFail();
        
        $riwayat = BookingFutsal::with(['lapangan'])
            ->where('no_hp', $bookingFutsal->no_hp)
            ->orderByDesc('start_datetime')
            ->get();

        return view('user.token.riwayat', compact('riwayat', 'bookingFutsal', 'token'));
    }

    public function batalkan($token)
    {
        $bookingFutsal = BookingFutsal::with(['booking', 'lapangan'])
            ->where('access_token', $token)->firstOrFail();

        $canCancel = $bookingFutsal->status === 'menunggu' && 
                     Carbon::parse($bookingFutsal->start_datetime)->gt(now()->addHours(2));

        if (!$canCancel) {
            return redirect()->route('user.token.show', $token)
                ->with('error', 'Booking tidak bisa dibatalkan karena sudah dikonfirmasi atau waktu bermain kurang dari 2 jam.');
        }

        return view('user.token.batalkan', compact('bookingFutsal', 'token'));
    }

    public function prosesBatalkan(Request $request, $token)
    {
        $bookingFutsal = BookingFutsal::with(['booking'])
            ->where('access_token', $token)->firstOrFail();

        $canCancel = $bookingFutsal->status === 'menunggu' && 
                     Carbon::parse($bookingFutsal->start_datetime)->gt(now()->addHours(2));

        if (!$canCancel) {
            return redirect()->route('user.token.show', $token)
                ->with('error', 'Booking tidak memenuhi syarat untuk dibatalkan.');
        }

        $bookingFutsal->update(['status' => 'dibatalkan']);
        
        if ($bookingFutsal->booking) {
            $bookingFutsal->booking->update(['status' => 'dibatalkan']);
        }

        return redirect()->route('user.token.show', $token)
            ->with('success', 'Booking berhasil dibatalkan.');
    }
}
