<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BookingAc;
use App\Models\Booking;
use App\Models\Pembatalan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AcDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $statusFilter = $request->get('status');

        $query = BookingAc::where('user_id', $user->id)
            ->with(['layanan', 'pembayaran', 'booking']);

        if ($statusFilter) {
            if ($statusFilter === 'canceled') {
                $query->whereHas('booking', function ($q) {
                    $q->where('status', 'dibatalkan');
                });
            } else {
                $query->where('status', $statusFilter)
                    ->whereHas('booking', function ($q) {
                        $q->where('status', '!=', 'dibatalkan');
                    });
            }
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(10);

        // Stats
        $stats = [
            'total' => BookingAc::where('user_id', $user->id)->count(),
            'pending' => BookingAc::where('user_id', $user->id)->where('status', 'menunggu')->whereHas('booking', fn($q) => $q->where('status', '!=', 'dibatalkan'))->count(),
            'proses' => BookingAc::where('user_id', $user->id)->where('status', 'proses')->count(),
            'selesai' => BookingAc::where('user_id', $user->id)->where('status', 'selesai')->count(),
        ];

        return view('user.ac.index', compact('bookings', 'stats', 'statusFilter'));
    }

    public function history()
    {
        $user = Auth::user();
        
        $history = BookingAc::where('user_id', $user->id)
            ->where('status', 'selesai')
            ->with(['layanan', 'teknisi', 'pembayaran', 'booking'])
            ->orderBy('tgl_kunjungan', 'desc')
            ->paginate(10);

        return view('user.ac.history', compact('history'));
    }

    public function show($id)
    {
        $booking = BookingAc::where('id', $id)
            ->where('user_id', Auth::id())
            ->with(['layanan', 'teknisi', 'rincianServis', 'pembayaran', 'booking'])
            ->firstOrFail();

        return response()->json($booking);
    }

    public function cancel(Request $request, $id)
    {
        $request->validate([
            'alasan' => 'required|string|max:255',
        ]);

        $bookingAc = BookingAc::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($bookingAc->status !== 'menunggu') {
            return redirect()->back()->with('error', 'Booking yang sudah diproses tidak dapat dibatalkan.');
        }

        DB::beginTransaction();
        try {
            // Update parent booking status
            $booking = Booking::findOrFail($bookingAc->booking_id);
            $booking->update(['status' => 'dibatalkan']);

            // Create cancellation record
            Pembatalan::create([
                'booking_id' => $booking->id,
                'alasan' => $request->alasan,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Booking berhasil dibatalkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal membatalkan booking. Silakan coba lagi.');
        }
    }
}
