<?php

namespace App\Http\Controllers\AdminServis;

use App\Http\Controllers\Controller;
use App\Models\BookingServis;
use App\Traits\Loggable;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    use Loggable;

    /**
     * Tampilkan semua booking dengan filter dan search.
     */
    public function index(Request $request)
    {
        $query = BookingServis::with(['pelanggan', 'teknisi', 'layananServis']);

        // Filter Tanggal
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal_booking', $request->tanggal);
        }

        // Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search Nama Pelanggan
        if ($request->filled('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('nama_lengkap', 'like', '%' . $request->search . '%');
            });
        }

        $bookings = $query->latest()->paginate(15)->withQueryString();

        // Hitung ketersediaan slot untuk tanggal yang difilter atau hari ini
        $tanggalSlot = $request->tanggal ?? now()->toDateString();
        $slotUsage   = BookingServis::whereDate('tanggal_booking', $tanggalSlot)
            ->whereNotIn('status', ['batal', 'selesai'])
            ->select('jam_booking', DB::raw('count(*) as total'))
            ->groupBy('jam_booking')
            ->pluck('total', 'jam_booking')
            ->toArray();

        return view('adminservis.booking.index', compact('bookings', 'slotUsage', 'tanggalSlot'));
    }

    /**
     * Tampilkan detail booking.
     */
    public function show($id)
    {
        $booking = BookingServis::with(['pelanggan', 'rincianServis', 'layananServis', 'teknisi'])
            ->findOrFail($id);

        return view('adminservis.booking.show', compact('booking'));
    }

    /**
     * Konfirmasi booking oleh Admin Servis.
     */
    public function konfirmasi($id)
    {
        $booking = BookingServis::findOrFail($id);
        $booking->update(['status' => 'dikonfirmasi']);

        $this->function_log('Servis', 'konfirmasi', 'Admin Servis mengkonfirmasi booking: ' . $booking->kode_booking . ' atas nama ' . ($booking->nama_pemesan ?? '-'));

        return back()->with('success', 'Booking berhasil dikonfirmasi.');
    }

    /**
     * Tolak booking oleh Admin Servis.
     */
    public function tolak(Request $request, $id)
    {
        $booking = BookingServis::findOrFail($id);
        $booking->update(['status' => 'batal']);

        $this->function_log('Servis', 'tolak', 'Admin Servis menolak booking: ' . $booking->kode_booking . ' atas nama ' . ($booking->nama_pemesan ?? '-'));

        return back()->with('success', 'Booking berhasil ditolak.');
    }
}