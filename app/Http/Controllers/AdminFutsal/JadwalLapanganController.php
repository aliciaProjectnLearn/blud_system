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
        $pengaturan = \App\Models\Pengaturan::first();
        return view('adminfutsal.jadwal.index', compact('pengaturan'));
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

        // --- Fitur Auto-Generate Jadwal Dinamis ---
        $pengaturan = \App\Models\Pengaturan::first();
        $lapangans = \App\Models\Lapangan::all();

        if ($pengaturan && $lapangans->count() > 0) {
            $jamBuka = Carbon::parse($pengaturan->jam_buka);
            $jamTutup = Carbon::parse($pengaturan->jam_tutup);

            // Clone/salin waktu mulai agar variabel $jamBuka tidak ikut bergeser secara referensi
            $currentStart = $jamBuka->copy();

            while ($currentStart < $jamTutup) {
                $jamMulai = $currentStart->format('H:i:s');
                $jamSelesai = $currentStart->copy()->addHour()->format('H:i:s');

                foreach ($lapangans as $lapangan) {
                    // firstOrCreate mengecek apakah kombinasi unik tersebut sudah ada,
                    // bila belum ada maka otomatis menjalankan JadwalLapangan::create(...)
                    JadwalLapangan::firstOrCreate([
                        'tanggal' => $tanggal,
                        'lapangan_id' => $lapangan->id,
                        'jam_mulai' => $jamMulai,
                    ], [
                        'jam_selesai' => $jamSelesai,
                        'status' => 'tersedia'
                    ]);
                }

                $currentStart->addHour();
            }
        }
        // --- Akhir Fitur Auto-Generate ---

        // --- Rekonsiliasi: Sinkronkan jadwal_lapangan.status dengan booking_futsal aktif ---
        // Ambil semua booking aktif untuk tanggal tersebut di semua lapangan
        $bookings = \App\Models\BookingFutsal::with('booking')
            ->where('type', 'regular')
            ->whereHas('booking', fn($q) => $q->whereNotIn('status', ['dibatalkan']))
            ->where(function($q) use ($tanggal) {
                $start = Carbon::parse($tanggal)->startOfDay();
                $end   = Carbon::parse($tanggal)->endOfDay();
                $q->whereBetween('start_datetime', [$start, $end]);
            })
            ->get();

        // Reset dulu semua slot hari itu ke 'tersedia' agar sinkron ulang
        JadwalLapangan::whereDate('tanggal', $tanggal)
            ->update(['status' => 'tersedia']);

        // Tandai kembali slot yang benar-benar memiliki booking aktif
        foreach ($bookings as $bf) {
            $jamMulaiBooked   = Carbon::parse($bf->start_datetime)->format('H:i:s');
            $jamSelesaiBooked = Carbon::parse($bf->end_datetime)->format('H:i:s');

            JadwalLapangan::where('lapangan_id', $bf->lapangan_id)
                ->whereDate('tanggal', $tanggal)
                ->where('jam_mulai', '>=', $jamMulaiBooked)
                ->where('jam_mulai', '<', $jamSelesaiBooked)
                ->update(['status' => 'terisi']);
        }
        // --- Akhir Rekonsiliasi ---

        // Query dengan eager loading dan grup per lapangan
        $lapangangList = \App\Models\Lapangan::all();
        $jadwal = JadwalLapangan::with('lapangan')
            ->whereDate('tanggal', $tanggal)
            ->orderBy('jam_mulai', 'asc')
            ->get();

        // Kelompokkan per lapangan_id
        $grouped = $jadwal->groupBy('lapangan_id');

        // Kembalikan Response JSON yang rapi
        return response()->json([
            'status'    => 'success',
            'tanggal'   => $tanggal,
            'lapangans' => $lapangangList,   // daftar semua lapangan
            'data'      => $jadwal,           // flat (untuk kompatibilitas)
            'grouped'   => $grouped,          // grouped per lapangan_id
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
