<?php

namespace App\Http\Controllers\AdminFutsal;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingFutsal;
use App\Models\JadwalLapangan;
use App\Models\Lapangan;
use App\Models\LogActivity;
use App\Models\Membership;
use App\Models\PembayaranFutsal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = BookingFutsal::with(['booking', 'user', 'lapangan']);

        // Filter by Date
        if ($request->filled('tanggal')) {
            $query->where('tgl_main', $request->tanggal);
        }

        // Filter by Status
        if ($request->filled('status')) {
            $query->whereHas('booking', function ($q) use ($request) {
                $q->where('status', $request->status);
            });
        }

        // Search by User Name / Email
        if ($request->filled('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Sort by nearest date and time
        $bookings = $query->orderBy('tgl_main', 'asc')
            ->orderBy('jam_mulai', 'asc')
            ->paginate(10);

        $lapangans = Lapangan::all();
        $users = User::all();
        $jadwalLapangan = JadwalLapangan::all();

        return view('adminfutsal.booking.index', compact('bookings', 'lapangans', 'users', 'jadwalLapangan'));
    }

    public function getAvailableSlots(Request $request)
    {
        $lapanganId = $request->lapangan_id;
        $tanggal = $request->tanggal;

        $jadwal = JadwalLapangan::all();

        // Ambil semua booking di lapangan & tanggal tersebut yang mengunci slot
        $bookedSlots = BookingFutsal::whereHas('booking', function ($q) {
                $q->whereIn('status', ['menunggu', 'dikonfirmasi', 'selesai']);
            })
            ->where('lapangan_id', $lapanganId)
            ->where('tgl_main', $tanggal)
            ->get();

        $available = collect();

        foreach ($jadwal as $j) {
            $proposedMulai = Carbon::parse($j->jam_mulai);
            // Default 1 jam durasi booking di UI
            $proposedSelesai = $proposedMulai->copy()->addHour();

            $proposedStartWithPadding = $proposedMulai->copy()->subMinutes(9)->format('H:i:s');
            $proposedEndWithPadding = $proposedSelesai->copy()->addMinutes(9)->format('H:i:s');

            $isOverlap = false;

            foreach ($bookedSlots as $booked) {
                // Konversi jam dari DB ke string untuk perbandingan simpel
                $existingMulai = Carbon::parse($booked->jam_mulai)->format('H:i:s');
                $existingSelesai = Carbon::parse($booked->jam_selesai)->format('H:i:s');

                // Logic Intersection persis seperti checkOverlap()
                if ($existingMulai < $proposedEndWithPadding && $existingSelesai > $proposedStartWithPadding) {
                    $isOverlap = true;
                    break;
                }
            }

            if (!$isOverlap) {
                $available->push($j);
            }
        }

        return response()->json($available->values());
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'lapangan_id' => 'required|exists:lapangan,id',
            'tgl_main' => 'required|date',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'jenis_pembayaran' => 'required|in:reguler,membership',
        ]);

        $jamMulai = Carbon::parse($request->jam_mulai);
        $jamSelesai = Carbon::parse($request->jam_selesai);
        $durasi = max(1, ceil($jamSelesai->diffInMinutes($jamMulai) / 60));

        DB::beginTransaction();
        try {
            // LOCK: Cegah Race Condition pada slot Lapangan yang sama
            $lapangan = Lapangan::where('id', $request->lapangan_id)->lockForUpdate()->firstOrFail();

            // LOCK: Ekstra pengamanan race condition di level table booking futsal untuk tanggal yang sama
            BookingFutsal::where('lapangan_id', $lapangan->id)
                ->where('tgl_main', $request->tgl_main)
                ->lockForUpdate()
                ->get();

            // VALIDASI: Overlap & 10 min jeda
            if ($this->checkOverlap($lapangan->id, $request->tgl_main, $request->jam_mulai, $request->jam_selesai)) {
                throw new \Exception('Waktu pemesanan bertabrakan atau tidak memenuhi jeda minimal 10 menit.');
            }

            // Membership Check
            $membership = null;
            if ($request->jenis_pembayaran === 'membership') {
                $membership = Membership::where('user_id', $request->user_id)
                    ->where('status', 'aktif')
                    ->lockForUpdate()
                    ->first();

                if (!$membership || $membership->sisa_kuota < $durasi) {
                    throw new \Exception('Kuota membership tidak mencukupi atau tidak aktif!');
                }
            }

            // BUAT BOOKING (Status awal menunggu)
            $booking = Booking::create([
                'user_id' => $request->user_id,
                'status' => 'menunggu',
            ]);

            BookingFutsal::create([
                'booking_id' => $booking->id,
                'user_id' => $request->user_id,
                'lapangan_id' => $lapangan->id,
                'tgl_main' => $request->tgl_main,
                'jam_mulai' => $request->jam_mulai,
                'jam_mulai_efektif' => $request->jam_mulai,
                'jam_selesai' => $request->jam_selesai,
                'durasi_main' => $durasi,
                'jenis_pembayaran' => $request->jenis_pembayaran,
            ]);

            if ($request->jenis_pembayaran === 'reguler') {
                $harga = $lapangan->harga_siang ?? 100000;
                PembayaranFutsal::create([
                    'booking_id' => $booking->id,
                    'tipe_pembayaran_id' => 2, // Tunai default
                    'jumlah_bayar' => $harga * $durasi,
                    'status' => 'menunggu',
                ]);
            } else {
                // Potong kuota membership & upgrade status
                $membership->gunakanKuota($durasi);
                $booking->update(['status' => 'dikonfirmasi']);
            }

            // LOG
            LogActivity::create([
                'user_id' => auth()->id(),
                'nama_user' => auth()->user()->name ?? 'Admin Futsal',
                'sistem' => 'Manajemen Booking Futsal',
                'aktivitas' => 'Tambah Booking Futsal',
                'deskripsi_aktivitas' => "Menambahkan booking untuk UserID: {$request->user_id} di LapanganID: {$lapangan->id}",
            ]);

            DB::commit();
            return back()->with('success', 'Booking berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function cancel($id)
    {
        DB::beginTransaction();
        try {
            $bookingFutsal = BookingFutsal::with('booking')->lockForUpdate()->findOrFail($id);
            $booking = $bookingFutsal->booking;

            if ($booking->status === 'dibatalkan') {
                throw new \Exception('Booking ini sudah dibatalkan sebelumnya.');
            }

            $booking->update(['status' => 'dibatalkan']);

            if ($bookingFutsal->jenis_pembayaran === 'membership') {
                $membership = Membership::where('user_id', $bookingFutsal->user_id)
                    ->orderBy('created_at', 'desc')->lockForUpdate()->first();
                    
                if ($membership) {
                    $membership->kembalikanKuota($bookingFutsal->durasi_main);
                }
            } else {
                $pembayaran = PembayaranFutsal::where('booking_id', $booking->id)->lockForUpdate()->first();
                if ($pembayaran) {
                    $pembayaran->update(['status' => 'dibatalkan']);
                }
            }

            LogActivity::create([
                'user_id' => auth()->id(),
                'nama_user' => auth()->user()->name ?? 'Admin Futsal',
                'sistem' => 'Manajemen Booking Futsal',
                'aktivitas' => 'Batal Booking',
                'deskripsi_aktivitas' => 'Membatalkan booking futsal BookingID: ' . $booking->id,
            ]);

            DB::commit();
            return back()->with('success', 'Booking berhasil dibatalkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'lapangan_id' => 'required|exists:lapangan,id',
            'tgl_main' => 'required|date',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
        ]);

        DB::beginTransaction();
        try {
            $lapangan = Lapangan::where('id', $request->lapangan_id)->lockForUpdate()->firstOrFail();
            $bookingFutsal = BookingFutsal::with('booking')->lockForUpdate()->findOrFail($id);
            
            if ($bookingFutsal->booking->status === 'dibatalkan' || $bookingFutsal->booking->status === 'selesai') {
                throw new \Exception('Booking sudah selesai/batal, tidak bisa direchedule.');
            }

            $jamMulai = Carbon::parse($request->jam_mulai);
            $jamSelesai = Carbon::parse($request->jam_selesai);
            $durasiBaru = max(1, ceil($jamSelesai->diffInMinutes($jamMulai) / 60));

            // LOCK: Ekstra pengamanan race condition di level table booking
            BookingFutsal::where('lapangan_id', $lapangan->id)
                ->where('tgl_main', $request->tgl_main)
                ->lockForUpdate()
                ->get();

            // VALIDASI: overlap excluding current booking
            if ($this->checkOverlap($lapangan->id, $request->tgl_main, $request->jam_mulai, $request->jam_selesai, $bookingFutsal->booking_id)) {
                throw new \Exception('Jadwal baru bertabrakan atau tidak memenuhi jeda 10 menit.');
            }

            if ($bookingFutsal->jenis_pembayaran === 'membership') {
                $membership = Membership::where('user_id', $bookingFutsal->user_id)->orderBy('created_at', 'desc')->lockForUpdate()->first();
                if ($membership) {
                    if ($durasiBaru > $bookingFutsal->durasi_main) {
                        $diff = $durasiBaru - $bookingFutsal->durasi_main;
                        if ($membership->sisa_kuota < $diff) {
                            throw new \Exception('Sisa kuota membership tidak mencukupi untuk penambahan waktu!');
                        }
                        $membership->gunakanKuota($diff);
                    } else if ($durasiBaru < $bookingFutsal->durasi_main) {
                        $diff = $bookingFutsal->durasi_main - $durasiBaru;
                        $membership->kembalikanKuota($diff);
                    }
                }
            } else {
                $harga = $lapangan->harga_siang ?? 100000;
                $pembayaran = PembayaranFutsal::where('booking_id', $bookingFutsal->booking_id)->first();
                if ($pembayaran) {
                    $pembayaran->update(['jumlah_bayar' => $harga * $durasiBaru]);
                }
            }

            $bookingFutsal->update([
                'lapangan_id' => $lapangan->id,
                'tgl_main' => $request->tgl_main,
                'jam_mulai' => $request->jam_mulai,
                'jam_selesai' => $request->jam_selesai,
                'durasi_main' => $durasiBaru,
            ]);

            LogActivity::create([
                'user_id' => auth()->id(),
                'nama_user' => auth()->user()->name ?? 'Admin Futsal',
                'sistem' => 'Manajemen Booking Futsal',
                'aktivitas' => 'Reschedule Booking',
                'deskripsi_aktivitas' => 'Merechedule jadwal BookingID: ' . $bookingFutsal->booking_id,
            ]);

            DB::commit();
            return back()->with('success', 'Jadwal berhasil direchedule.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function selesai($id)
    {
        DB::beginTransaction();
        try {
            $bookingFutsal = BookingFutsal::with('booking')->lockForUpdate()->findOrFail($id);
            $booking = $bookingFutsal->booking;
            
            if ($booking->status === 'dibatalkan' || $booking->status === 'selesai') {
                 throw new \Exception('Status tidak valid untuk diselesaikan.');
            }

            $booking->update(['status' => 'selesai']);

            if ($bookingFutsal->jenis_pembayaran === 'reguler') {
                $pembayaran = PembayaranFutsal::where('booking_id', $booking->id)->first();
                if ($pembayaran) {
                    // Update ke 'lunas' atau 'verifikasi' untuk nandain bayar tunai
                    $pembayaran->update(['status' => 'verifikasi']);
                }
            }

            LogActivity::create([
                'user_id' => auth()->id(),
                'nama_user' => auth()->user()->name ?? 'Admin Futsal',
                'sistem' => 'Manajemen Booking Futsal',
                'aktivitas' => 'Selesaikan Booking',
                'deskripsi_aktivitas' => 'Tandai Selesai untuk BookingID: ' . $booking->id,
            ]);

            DB::commit();
            return back()->with('success', 'Booking berhasil diselesaikan (Sesi telah berakhir).');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Logic Overlap Teraman.
     * Menggunakan Intersection logic: (A.start < B.end) AND (A.end > B.start)
     */
    private function checkOverlap($lapanganId, $tanggal, $timeStart, $timeEnd, $excludeBookingId = null)
    {
        // Tolerasni jeda 10 menit (A.start - 9m < B.end) and (A.end + 9m > B.start)
        // Setara: existing_start < proposed_end + 9m  && existing_end > proposed_start - 9m
        $proposedEndWithPadding = Carbon::parse($timeEnd)->addMinutes(9)->format('H:i:s');
        $proposedStartWithPadding = Carbon::parse($timeStart)->subMinutes(9)->format('H:i:s');

        return BookingFutsal::whereHas('booking', function ($q) {
                // Semua jadwal ini MENGUNCI lapangan
                $q->whereIn('status', ['menunggu', 'dikonfirmasi', 'selesai']);
            })
            ->where('lapangan_id', $lapanganId)
            ->where('tgl_main', $tanggal)
            ->where(function ($query) use ($proposedStartWithPadding, $proposedEndWithPadding) {
                // Logika Intersection menggunakan kolom string waktu murni
                $query->where('jam_mulai', '<', $proposedEndWithPadding)
                      ->where('jam_selesai', '>', $proposedStartWithPadding);
            })
            ->when($excludeBookingId, function ($query, $id) {
                return $query->where('booking_id', '!=', $id);
            })
            ->exists();
    }
}
