<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingFutsal;
use App\Models\JadwalLapangan;
use App\Models\Lapangan;
use App\Models\Membership;
use App\Models\PaketMembership;
use App\Models\PembayaranFutsal;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;

class FutsalBookingController extends Controller
{
    public function landing()
    {
        $lapangans = Lapangan::all();
        $paketMemberships = PaketMembership::where('status', 'aktif')->get();
        $membershipAktif = null; // Halaman publik

        return view('user.futsal.landing', compact('lapangans', 'paketMemberships', 'membershipAktif'));
    }

    public function membershipForm()
    {
        $paketMemberships = PaketMembership::where('status', 'aktif')->get();
        $membershipAktif = null; // Jalur publik

        return view('user.futsal.membership', compact('paketMemberships', 'membershipAktif'));
    }

    public function membershipStore(Request $request)
    {
        $request->validate([
            'paket_membership_id' => 'required|exists:paket_membership,id',
        ]);

        return redirect()->route('user.futsal.booking.form', ['paket_id' => $request->paket_membership_id])
            ->with('info', 'Silahkan isi data diri Anda untuk melanjutkan pembelian membership dan booking.');
    }

    public function index()
    {
        $lapangans = Lapangan::all();
        $paketMemberships = PaketMembership::where('status', 'aktif')->get();
        
        $membership = null;
        $isFirstBooking = false;

        if (Auth::check()) {
            $membership = Membership::where('user_id', Auth::id())
                ->where('status', 'aktif')
                ->with(['paket', 'transaksi'])
                ->first();

            if ($membership) {
                $existingPayment = PembayaranFutsal::where('booking_id', $membership->transaksi_id)
                    ->where('jenis_transaksi', 'membership')
                    ->first();
                
                if (!$existingPayment || in_array($existingPayment->status, ['menunggu', 'dibatalkan'])) {
                    $isFirstBooking = true;
                }
            }
        }

        return view('user.futsal.booking', compact('lapangans', 'paketMemberships', 'membership', 'isFirstBooking'));
    }

    public function checkAvailability(Request $request)
    {
        $request->validate([
            'lapangan_id' => 'required|exists:lapangan,id',
            'tanggal'     => 'required|date|after_or_equal:today',
        ]);

        $pengaturan = \App\Models\Pengaturan::first();
        $jamBuka = $pengaturan->jam_buka ?? '08:00';
        $jamTutup = $pengaturan->jam_tutup ?? '22:00';
        $tanggal = Carbon::parse($request->tanggal);

        $adaEvent = BookingFutsal::where('lapangan_id', $request->lapangan_id)
            ->where('type', 'event')
            ->whereHas('booking', fn($q) => $q->whereNotIn('status', ['dibatalkan']))
            ->where('start_datetime', '<=', $tanggal->copy()->endOfDay())
            ->where('end_datetime', '>=', $tanggal->copy()->startOfDay())
            ->first();

        if ($adaEvent) {
            return response()->json([
                'success' => false,
                'event' => true,
                'pesan' => 'Lapangan ini sedang digunakan untuk event ('.
                    Carbon::parse($adaEvent->start_datetime)->format('d M Y')
                    . ' s/d '
                    . Carbon::parse($adaEvent->end_datetime)->format('d M Y')
                    . '). Silahkan pilih tanggal lain.',
            ]);
        }

        if ($pengaturan) {
            $currentStart = Carbon::parse($jamBuka);
            $jamTutupSettings = Carbon::parse($jamTutup);
            
            while ($currentStart < $jamTutupSettings) {
                $jamMulai = $currentStart->format('H:i:s');
                $jamSelesai = $currentStart->copy()->addHour()->format('H:i:s');
                
                JadwalLapangan::firstOrCreate([
                    'tanggal' => $request->tanggal,
                    'lapangan_id' => $request->lapangan_id,
                    'jam_mulai' => $jamMulai,
                ], [
                    'jam_selesai' => $jamSelesai,
                    'status' => 'tersedia'
                ]);
                
                $currentStart->addHour();
            }
        }

        $jadwals = JadwalLapangan::where('lapangan_id', $request->lapangan_id)
            ->where('tanggal', $request->tanggal)
            ->where('jam_mulai', '>=', $jamBuka)
            ->where('jam_selesai', '<=', $jamTutup)
            ->orderBy('jam_mulai')
            ->get(['id', 'jam_mulai', 'jam_selesai', 'status']);

        $slots = $jadwals->map(function ($slot) use ($request) {
            $startDatetime = Carbon::parse($request->tanggal . ' ' . $slot->jam_mulai);
            $endDatetime = Carbon::parse($request->tanggal . ' ' . $slot->jam_selesai);

            $isBooked = BookingFutsal::where('lapangan_id', $request->lapangan_id)
                ->whereHas('booking', fn ($q) => $q->whereNotIn ('status', ['dibatalkan']))
                ->where(function($q) use ($startDatetime, $endDatetime) {
                    $q->where('start_datetime', '<', $endDatetime)
                    ->where('end_datetime', '>', $startDatetime);
                })
                ->exists();
                
            $booked = $isBooked || $slot->status === 'terisi';

            return [
                'id' => $slot->id,
                'jam_mulai' => \Carbon\Carbon::parse($slot->jam_mulai)->format('H:i'),
                'jam_selesai' => \Carbon\Carbon::parse($slot->jam_selesai)->format('H:i'),
                'jam_mulai_display' => \Carbon\Carbon::parse($slot->jam_mulai)->addMinutes(10)->format('H:i'),
                'jam_selesai_display' => \Carbon\Carbon::parse($slot->jam_selesai)->addMinutes(10)->format('H:i'),
                'booked' => $booked,
            ];
        });

        return response()->json([
            'success' => true,
            'slots'   => $slots,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'             => 'required|string|max:255',
            'no_hp'            => 'required|string|max:20',
            'lapangan_id'      => 'required|exists:lapangan,id',
            'type'             => 'required|in:regular,event',
            'jenis_pembayaran' => 'required|in:reguler,membership',
            'tanggal'          => 'nullable|required_if:type,regular|date|after_or_equal:today',
            'jam_mulai_id'     => 'nullable|required_if:type,regular|exists:jadwal_lapangan,id',
            'durasi_main'      => 'nullable|required_if:type,regular|integer|min:1|max:3',
            'start_datetime'   => 'nullable|required_if:type,event|date|after_or_equal:today',
            'end_datetime'     => 'nullable|required_if:type,event|date|after:start_datetime',
            'bukti_pembayaran' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // 1. Find or Create User
        $user = User::where('no_hp', $validated['no_hp'])->first();
        if (!$user) {
            $user = User::create([
                'name' => explode(' ', $validated['nama'])[0],
                'nama_lengkap' => $validated['nama'],
                'no_hp' => $validated['no_hp'],
                'role' => 'pelanggan',
                'password' => bcrypt(Str::random(16)), // Dummy password
            ]);
            
            $role = Role::where('nama', 'pelanggan')->first();
            if ($role) {
                $user->roles()->attach($role->id);
            }
        }

        $userId = $user->id;
        $accessToken = bin2hex(random_bytes(32));

        DB::beginTransaction();
        try {
            if ($validated['type'] === 'regular') {
                $slot = JadwalLapangan::where('id', $validated['jam_mulai_id'])
                    ->where('lapangan_id', $validated['lapangan_id'])
                    ->where('tanggal', $validated['tanggal'])
                    ->where('status', 'tersedia')
                    ->first();

                if (!$slot) throw new \Exception('Slot jadwal tidak tersedia.');

                $startDatetime = Carbon::parse($validated['tanggal'] . ' ' . $slot->jam_mulai);
                $endDatetime   = $startDatetime->copy()->addHours((int) $validated['durasi_main']);
                
                // Overlap check
                $overlap = BookingFutsal::where('lapangan_id', $validated['lapangan_id'])
                    ->whereHas('booking', fn($q) => $q->whereNotIn('status', ['dibatalkan']))
                    ->where(function ($q) use ($startDatetime, $endDatetime) {
                        $q->where('start_datetime', '<', $endDatetime)
                        ->where('end_datetime', '>', $startDatetime);
                    })
                    ->exists();

                if ($overlap) throw new \Exception('Waktu sudah bentrok.');

                $pengaturan = \App\Models\Pengaturan::first();
                $hargaRegulerPerHour = $pengaturan->harga_reguler_futsal ?? 75000;

                $booking = Booking::create([
                    'user_id' => $userId,
                    'status'  => 'menunggu',
                    'access_token' => $accessToken,
                ]);

                BookingFutsal::create([
                    'booking_id'       => $booking->id,
                    'user_id'          => $userId,
                    'lapangan_id'      => $validated['lapangan_id'],
                    'start_datetime'   => $startDatetime,
                    'end_datetime'     => $endDatetime,
                    'type'             => 'regular',
                    'jenis_pembayaran' => $validated['jenis_pembayaran'],
                    'access_token'     => $accessToken,
                ]);

                // Payment logic
                $buktiPath = null;
                if (request()->hasFile('bukti_pembayaran')) {
                    $buktiPath = request()->file('bukti_pembayaran')->store('bukti_pembayaran_futsal', 'public');
                }

                PembayaranFutsal::create([
                    'booking_id'         => $booking->id,
                    'jenis_transaksi'    => 'booking',
                    'tipe_pembayaran_id' => request('tipe_pembayaran_id') ?? 2,
                    'jumlah_bayar'       => $validated['durasi_main'] * $hargaRegulerPerHour,
                    'status'             => 'menunggu',
                    'tgl_bayar'          => now(),
                    'bukti'              => $buktiPath,
                ]);

                // Update Jadwal
                JadwalLapangan::where('lapangan_id', $validated['lapangan_id'])
                    ->where('tanggal', $validated['tanggal'])
                    ->where('jam_mulai', '>=', $slot->jam_mulai)
                    ->where('jam_mulai', '<', $endDatetime->format('H:i:s'))
                    ->update(['status' => 'terisi']);

            } else {
                // FLOW EVENT
                $startDatetime = Carbon::parse($validated['start_datetime']);
                $endDatetime   = Carbon::parse($validated['end_datetime']);

                $booking = Booking::create([
                    'user_id' => $userId,
                    'status'  => 'menunggu',
                    'access_token' => $accessToken,
                ]);

                $pengaturan = \App\Models\Pengaturan::first();
                $hargaEventPerDay = $pengaturan->harga_event_futsal ?? 800000;
                $durasiHari = max(1, $startDatetime->diffInDays($endDatetime));

                BookingFutsal::create([
                    'booking_id'       => $booking->id,
                    'user_id'          => $userId,
                    'lapangan_id'      => $validated['lapangan_id'],
                    'start_datetime'   => $startDatetime,
                    'end_datetime'     => $endDatetime,
                    'type'             => 'event',
                    'jenis_pembayaran' => 'reguler',
                    'access_token'     => $accessToken,
                ]);

                $buktiPath = null;
                if (request()->hasFile('bukti_pembayaran')) {
                    $buktiPath = request()->file('bukti_pembayaran')->store('bukti_pembayaran_futsal', 'public');
                }

                PembayaranFutsal::create([
                    'booking_id'         => $booking->id,
                    'jenis_transaksi'    => 'event',
                    'tipe_pembayaran_id' => request('tipe_pembayaran_id') ?? 2,
                    'jumlah_bayar'       => $hargaEventPerDay * $durasiHari,
                    'status'             => 'menunggu',
                    'tgl_bayar'          => now(),
                    'bukti'              => $buktiPath,
                ]);
            }

            DB::commit();
            return redirect()->route('user.token.show', $accessToken)->with('success', 'Booking berhasil dibuat!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }
}
