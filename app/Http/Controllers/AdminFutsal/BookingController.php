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

        // Filter by Date (using start_datetime instead of tgl_main)
        if ($request->filled('tanggal')) {
            $query->whereDate('start_datetime', $request->tanggal);
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
        $bookings = $query->orderBy('start_datetime', 'asc')->get();

        $lapangans = Lapangan::all();
        $users = User::all();
        $jadwalLapangan = JadwalLapangan::all();
        $pengaturan = \App\Models\Pengaturan::first();

        return view('adminfutsal.booking.index', compact('bookings', 'lapangans', 'users', 'jadwalLapangan', 'pengaturan'));
    }

    public function getAvailableSlots(Request $request)
    {
        $lapanganId = $request->lapangan_id;
        $tanggalStr = $request->tanggal;
        
        $tanggalStart = Carbon::parse($tanggalStr)->startOfDay();
        $tanggalEnd = Carbon::parse($tanggalStr)->endOfDay();

        $jadwal = JadwalLapangan::all();

        // Ambil semua booking di lapangan yang bersinggungan hari ini (termasuk event multi-hari)
        $bookedSlots = BookingFutsal::whereHas('booking', function ($q) {
                $q->whereIn('status', ['menunggu', 'dikonfirmasi', 'selesai']);
            })
            ->where('lapangan_id', $lapanganId)
            ->where(function($query) use ($tanggalStart, $tanggalEnd) {
                $query->where('start_datetime', '<=', $tanggalEnd)
                      ->where('end_datetime', '>=', $tanggalStart);
            })
            ->get();

        $available = collect();

        foreach ($jadwal as $j) {
            $proposedFullStart = Carbon::parse($tanggalStr . ' ' . $j->jam_mulai);
            $proposedFullEnd = $proposedFullStart->copy()->addHour();

            $proposedStartWithPadding = $proposedFullStart->copy()->subMinutes(9);
            $proposedEndWithPadding = $proposedFullEnd->copy()->addMinutes(9);

            $isOverlap = false;

            foreach ($bookedSlots as $booked) {
                $existingMulai = Carbon::parse($booked->start_datetime);
                $existingSelesai = Carbon::parse($booked->end_datetime);

                // Logic Intersection datetime
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
            'type' => 'required|in:regular,event',
            'tgl_main' => 'required_if:type,regular|nullable|date',
            'jam_mulai' => 'required_if:type,regular|nullable|date_format:H:i',
            'jam_selesai' => 'required_if:type,regular|nullable|date_format:H:i|after:jam_mulai',
            'start_datetime' => 'required_if:type,event|nullable|date',
            'end_datetime' => 'required_if:type,event|nullable|date|after:start_datetime',
            'jenis_pembayaran' => 'required|in:reguler,membership',
        ]);

        if ($request->type === 'event') {
            $startDateTime = Carbon::parse($request->start_datetime);
            $endDateTime = Carbon::parse($request->end_datetime);
            $durasi = max(1, ceil($endDateTime->diffInDays($startDateTime)));
        } else {
            $startDateTime = Carbon::parse($request->tgl_main . ' ' . $request->jam_mulai);
            $endDateTime = Carbon::parse($request->tgl_main . ' ' . $request->jam_selesai);
            $durasi = max(1, ceil($endDateTime->diffInMinutes($startDateTime) / 60));
        }

        DB::beginTransaction();
        try {
            $lapangan = Lapangan::where('id', $request->lapangan_id)->lockForUpdate()->firstOrFail();

            // VALIDASI: Overlap & 10 min jeda
            if ($this->checkOverlap($lapangan->id, $startDateTime, $endDateTime)) {
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
                'type' => $request->type,
                'start_datetime' => $startDateTime->format('Y-m-d H:i:s'),
                'end_datetime' => $endDateTime->format('Y-m-d H:i:s'),
                'jenis_pembayaran' => $request->jenis_pembayaran,
            ]);

            if ($request->jenis_pembayaran === 'reguler') {
                $harga = $request->type === 'event' ? ($lapangan->harga_siang ?? 100000) * 10 : ($lapangan->harga_siang ?? 100000); 
                // Untuk event asumsikan harganya fix dikali 10 jam/hari, tapi sebagai fallback, kita samakan durasi kali harga
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

            LogActivity::create([
                'user_id' => auth()->id(),
                'nama_user' => auth()->user()->name ?? 'Admin Futsal',
                'sistem' => 'Manajemen Booking Futsal',
                'aktivitas' => 'Tambah Booking Futsal',
                'deskripsi_aktivitas' => "Menambahkan booking untuk UserID: {$request->user_id} di LapanganID: {$lapangan->id}",
            ]);

            if ($request->type === 'regular') {
                for ($i = 0; $i < $durasi; $i++) {
                    $slotStart = $startDateTime->copy()->addHours($i)->format('H:i:s');
                    JadwalLapangan::where('lapangan_id', $lapangan->id)
                        ->where('tanggal', $request->tgl_main)
                        ->where('jam_mulai', $slotStart)
                        ->update(['status' => 'terisi']);
                }
            }

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
                $durasiBack = $bookingFutsal->type === 'event' ? $bookingFutsal->durasi_hari : $bookingFutsal->durasi_jam;
                $membership = Membership::where('user_id', $bookingFutsal->user_id)
                    ->orderBy('created_at', 'desc')->lockForUpdate()->first();

                if ($membership) {
                    $membership->kembalikanKuota($durasiBack);
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

            if ($bookingFutsal->type === 'regular') {
                $jamBatal = Carbon::parse($bookingFutsal->start_datetime);
                $durasiStr = $bookingFutsal->durasi_jam;
                for ($i = 0; $i < $durasiStr; $i++) {
                    $slotStart = $jamBatal->copy()->addHours($i)->format('H:i:s');
                    JadwalLapangan::where('lapangan_id', $bookingFutsal->lapangan_id)
                        ->where('tanggal', $jamBatal->format('Y-m-d'))
                        ->where('jam_mulai', $slotStart)
                        ->update(['status' => 'tersedia']);
                }
            }

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
            'type' => 'required|in:regular,event',
            'tgl_main' => 'required_if:type,regular|nullable|date',
            'jam_mulai' => 'required_if:type,regular|nullable|date_format:H:i',
            'jam_selesai' => 'required_if:type,regular|nullable|date_format:H:i|after:jam_mulai',
            'start_datetime' => 'required_if:type,event|nullable|date',
            'end_datetime' => 'required_if:type,event|nullable|date|after:start_datetime',
        ]);

        DB::beginTransaction();
        try {
            $lapangan = Lapangan::where('id', $request->lapangan_id)->lockForUpdate()->firstOrFail();
            $bookingFutsal = BookingFutsal::with('booking')->lockForUpdate()->findOrFail($id);

            if ($bookingFutsal->booking->status === 'dibatalkan' || $bookingFutsal->booking->status === 'selesai') {
                throw new \Exception('Booking sudah selesai/batal, tidak bisa direchedule.');
            }

            if ($request->type === 'event') {
                $startDateTime = Carbon::parse($request->start_datetime);
                $endDateTime = Carbon::parse($request->end_datetime);
                $durasiBaru = max(1, ceil($endDateTime->diffInDays($startDateTime)));
            } else {
                $startDateTime = Carbon::parse($request->tgl_main . ' ' . $request->jam_mulai);
                $endDateTime = Carbon::parse($request->tgl_main . ' ' . $request->jam_selesai);
                $durasiBaru = max(1, ceil($endDateTime->diffInMinutes($startDateTime) / 60));
            }

            // VALIDASI: overlap excluding current booking
            if ($this->checkOverlap($lapangan->id, $startDateTime, $endDateTime, $bookingFutsal->booking_id)) {
                throw new \Exception('Jadwal baru bertabrakan atau tidak memenuhi jeda 10 menit.');
            }

            $durasiLama = $bookingFutsal->type === 'event' ? $bookingFutsal->durasi_hari : $bookingFutsal->durasi_jam;

            if ($bookingFutsal->jenis_pembayaran === 'membership') {
                $membership = Membership::where('user_id', $bookingFutsal->user_id)->orderBy('created_at', 'desc')->lockForUpdate()->first();
                if ($membership) {
                    if ($durasiBaru > $durasiLama) {
                        $diff = $durasiBaru - $durasiLama;
                        if ($membership->sisa_kuota < $diff) {
                            throw new \Exception('Sisa kuota membership tidak mencukupi untuk penambahan waktu!');
                        }
                        $membership->gunakanKuota($diff);
                    } else if ($durasiBaru < $durasiLama) {
                        $diff = $durasiLama - $durasiBaru;
                        $membership->kembalikanKuota($diff);
                    }
                }
            } else {
                $harga = $request->type === 'event' ? ($lapangan->harga_siang ?? 100000) * 10 : ($lapangan->harga_siang ?? 100000);
                $pembayaran = PembayaranFutsal::where('booking_id', $bookingFutsal->booking_id)->first();
                if ($pembayaran) {
                    $pembayaran->update(['jumlah_bayar' => $harga * $durasiBaru]);
                }
            }

            if ($bookingFutsal->type === 'regular') {
                $oldJamMulai = Carbon::parse($bookingFutsal->start_datetime);
                for ($i = 0; $i < $durasiLama; $i++) {
                    $slotStart = $oldJamMulai->copy()->addHours($i)->format('H:i:s');
                    JadwalLapangan::where('lapangan_id', $bookingFutsal->lapangan_id)
                        ->where('tanggal', $oldJamMulai->format('Y-m-d'))
                        ->where('jam_mulai', $slotStart)
                        ->update(['status' => 'tersedia']);
                }
            }

            if ($request->type === 'regular') {
                for ($i = 0; $i < $durasiBaru; $i++) {
                    $slotStart = $startDateTime->copy()->addHours($i)->format('H:i:s');
                    JadwalLapangan::where('lapangan_id', $lapangan->id)
                        ->where('tanggal', $startDateTime->format('Y-m-d'))
                        ->where('jam_mulai', $slotStart)
                        ->update(['status' => 'terisi']);
                }
            }

            $bookingFutsal->update([
                'lapangan_id' => $lapangan->id,
                'type' => $request->type,
                'start_datetime' => $startDateTime->format('Y-m-d H:i:s'),
                'end_datetime' => $endDateTime->format('Y-m-d H:i:s'),
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

    private function checkOverlap($lapanganId, $proposedStart, $proposedEnd, $excludeBookingId = null)
    {
        $proposedStartWithPadding = Carbon::parse($proposedStart)->subMinutes(9)->format('Y-m-d H:i:s');
        $proposedEndWithPadding = Carbon::parse($proposedEnd)->addMinutes(9)->format('Y-m-d H:i:s');

        return BookingFutsal::whereHas('booking', function ($q) {
                $q->whereIn('status', ['menunggu', 'dikonfirmasi', 'selesai']);
            })
            ->where('lapangan_id', $lapanganId)
            ->where(function ($query) use ($proposedStartWithPadding, $proposedEndWithPadding) {
                // Logika Intersection datetime
                $query->where('start_datetime', '<', $proposedEndWithPadding)
                      ->where('end_datetime', '>', $proposedStartWithPadding);
            })
            ->when($excludeBookingId, function ($query, $id) {
                return $query->where('booking_id', '!=', $id);
            })
            ->exists();
    }
}
