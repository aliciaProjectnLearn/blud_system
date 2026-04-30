<?php

namespace App\Http\Controllers\AdminServis;

use App\Http\Controllers\Controller;
use App\Models\BookingServis;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    /**
     * Tampilkan semua booking dengan filter dan search.
     */
    public function index(Request $request)
    {
        $query = BookingServis::with(['pelanggan', 'teknisi']);

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
        $slotUsage = BookingServis::whereDate('tanggal_booking', $tanggalSlot)
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
        $booking = BookingServis::with(['pelanggan', 'teknisi', 'rincianServis'])->findOrFail($id);
        
        // Ambil daftar teknisi untuk dropdown assignment
        $listTeknisi = User::whereHas('roles', function ($q) {
            $q->where('nama', 'Teknisi')
              ->orWhere('nama', 'like', 'Teknisi%');
        })->get();

        return view('adminservis.booking.show', compact('booking', 'listTeknisi'));
    }

    /**
     * Update status booking.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:menunggu,diproses,selesai,batal',
        ]);

        $booking = BookingServis::findOrFail($id);

        if ($booking->status === 'batal') {
            return back()->with('error', 'Booking yang sudah batal tidak bisa diubah statusnya.');
        }

        $booking->update([
            'status' => $request->status,
        ]);

        return back()->with('success', 'Status booking berhasil diperbarui.');
    }

    /**
     * Assign teknisi ke booking.
     */
    public function assignTeknisi(Request $request, $id)
    {
        $request->validate([
            'teknisi_id' => 'required|exists:users,id',
        ]);

        $booking = BookingServis::findOrFail($id);

        if ($booking->status === 'batal') {
            return back()->with('error', 'Tidak bisa assign teknisi ke booking yang sudah batal.');
        }

        // Validasi role teknisi
        $teknisi = User::findOrFail($request->teknisi_id);
        if (!$teknisi->hasRole('Teknisi') && !$teknisi->hasRole('Teknisi Motor') && !$teknisi->hasRole('Teknisi Mobil')) {
            return back()->with('error', 'User yang dipilih bukan teknisi.');
        }

        $booking->update([
            'teknisi_id' => $request->teknisi_id,
        ]);

        return back()->with('success', 'Teknisi berhasil ditugaskan.');
    }
}
