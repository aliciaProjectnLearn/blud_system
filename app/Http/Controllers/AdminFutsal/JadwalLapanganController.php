<?php

namespace App\Http\Controllers\AdminFutsal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JadwalLapangan;
use Illuminate\Support\Carbon;

class JadwalLapanganController extends Controller
{
    /**
     * Menampilkan view halaman kelola jadwal lapangan (Frontend)
     */
    public function index()
    {
        return view('adminfutsal.jadwal.index');
    }

    /**
     * API Internal: Ambil jadwal berdasarkan tanggal
     */
    public function getJadwalByTanggal(Request $request)
    {
        // Ambil parameter tanggal dari query string request
        $tanggal = $request->query('tanggal');

        // Jika tidak ada/kosong, gunakan hari ini
        if (empty($tanggal)) {
            $tanggal = Carbon::today()->format('Y-m-d');
        }

        // Query dengan eager loading
        $jadwal = JadwalLapangan::with('lapangan')
            ->whereDate('tanggal', $tanggal)
            ->orderBy('jam_mulai', 'asc')
            ->get();

        // Kembalikan Response JSON yang rapi
        return response()->json([
            'status'  => 'success',
            'tanggal' => $tanggal,
            'data'    => $jadwal
        ]);
    }

    /**
     * API Internal: Mengunci slot jadwal (Booking) untuk mencegah double-booking
     */
    public function updateStatusBooking(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:jadwal_lapangan,id'
        ]);

        $jadwal = JadwalLapangan::find($request->id);

        if ($jadwal->status !== 'tersedia') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Slot jadwal ini sudah dibooking'
            ], 400); // HTTP Status 400 (Bad Request)
        }

        $jadwal->update(['status' => 'terisi']);

        return response()->json([
            'status'  => 'success',
            'message' => 'Slot jadwal berhasil diamankan',
            'data'    => $jadwal
        ]);
    }

    /**
     * API Internal: Membuka kunci jadwal (Batal Booking)
     */
    public function batalBooking(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:jadwal_lapangan,id'
        ]);

        $jadwal = JadwalLapangan::find($request->id);

        $jadwal->update(['status' => 'tersedia']);

        return response()->json([
            'status'  => 'success',
            'message' => 'Batal! Slot jadwal kembali tersedia',
            'data'    => $jadwal
        ]);
    }
}
