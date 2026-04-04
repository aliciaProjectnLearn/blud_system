<?php

namespace App\Http\Controllers\AdminAc;

use App\Http\Controllers\Controller;
use App\Models\BookingAc;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    // ── Daftar Booking ──────────────────────────────────
    public function index(Request $request)
    {
        $status = $request->status;

        $bookings = BookingAc::with(['user', 'layanan', 'teknisi'])
            ->when($status, fn($q) => $q->where('status', $status))
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        $statusList = ['menunggu', 'proses', 'selesai'];

        $teknisiTersedia = User::whereHas('roles', fn($q) => $q->where('nama', 'Teknisi'))
        ->whereNotIn('id', function ($q) {
            $q->select('teknisi_id')
                ->from('booking_ac')
                ->where('status', 'proses')
                ->whereNotNull('teknisi_id');
        })
        ->get();

        return view('adminac.booking.index', compact('bookings', 'status', 'statusList', 'teknisiTersedia'));
    }

    // ── Detail Booking ──────────────────────────────────
    public function show($id)
    {
        $booking = BookingAc::with(['user', 'layanan', 'teknisi'])->findOrFail($id);

        $teknisiTersedia = User::whereHas('roles', fn($q) => $q->where('nama', 'Teknisi'))
            ->whereNotIn('id', function ($q) {
                $q->select('teknisi_id')
                    ->from('booking_ac')
                    ->where('status', 'proses')
                    ->whereNotNull('teknisi_id');
            })
            ->get();

        return view('adminac.booking.show', compact('booking', 'teknisiTersedia'));
    }

    // ── Approve Booking (menunggu → proses) ─────────────
    public function approve(Request $request, $id)
    {
        $request->validate([
            'teknisi_id' => 'required|exists:users,id',
        ]);

        $booking = BookingAc::findOrFail($id);

        if ($booking->status !== 'menunggu') {
            return back()->with('error', 'Booking tidak dapat di-approve.');
        }

        // Cek apakah teknisi sedang sibuk
        $sedangSibuk = DB::table('booking_ac')
            ->where('teknisi_id', $request->teknisi_id)
            ->where('status', 'proses')
            ->exists();

        if ($sedangSibuk) {
            return back()->with('error', 'Teknisi sedang sibuk, pilih teknisi lain.');
        }

        $booking->update([
            'teknisi_id' => $request->teknisi_id,
            'status'     => 'proses',
        ]);

        return back()->with('success', 'Booking berhasil di-approve dan teknisi ditugaskan.');
    }

    // ── Selesai ─────────────────────────────────────────
    public function selesai($id)
    {
        $booking = BookingAc::findOrFail($id);

        if ($booking->status !== 'proses') {
            return back()->with('error', 'Booking harus berstatus proses untuk diselesaikan.');
        }

        $booking->update(['status' => 'selesai']);

        return back()->with('success', 'Booking berhasil diselesaikan.');
    }
}
