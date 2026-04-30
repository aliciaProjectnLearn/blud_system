<?php

namespace App\Http\Controllers\KasirFutsal;

use App\Http\Controllers\Controller;
use App\Models\BookingFutsal;
use App\Models\LogActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingKasirFutsalController extends Controller
{
    public function index(Request $request)
    {
        $query = BookingFutsal::with(['lapangan', 'user', 'pembayaranFutsal'])
            ->where(function ($q) {
                $q->whereDate('start_datetime', today())
                  ->orWhere('status', 'menunggu');
            });

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->latest('start_datetime')
            ->paginate(15)
            ->withQueryString();

        return view('kasirfutsal.booking.index', compact('bookings'));
    }

    public function show($id)
    {
        $booking = BookingFutsal::with(['lapangan', 'user', 'pembayaranFutsal.tipePembayaran'])->findOrFail($id);
        return view('kasirfutsal.booking.show', compact('booking'));
    }

    public function konfirmasi($id)
    {
        $booking = BookingFutsal::findOrFail($id);

        if ($booking->status !== 'menunggu') {
            return back()->with('error', 'Booking ini tidak dalam status menunggu.');
        }

        DB::beginTransaction();
        try {
            $booking->status = 'dikonfirmasi';
            $booking->save();

        LogActivity::create([
            'user_id'              => auth()->id(),
            'nama_user'            => auth()->user()->name,
            'sistem'               => 'Futsal',
            'aktivitas'            => 'Konfirmasi Booking',
            'deskripsi_aktivitas'  => 'Kasir Futsal mengkonfirmasi booking ID: ' . $booking->id,
        ]);

            DB::commit();
            return back()->with('success', 'Booking berhasil dikonfirmasi.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengkonfirmasi booking: ' . $e->getMessage());
        }
    }
}
