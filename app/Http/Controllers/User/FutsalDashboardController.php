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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class FutsalDashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard futsal user
     */
    public function index()
    {
        $userId = Auth::id();

        // 1. Ringkasan Data
        $summary = [
            'total' => BookingFutsal::where('user_id', $userId)->whereHas('booking')->count(),
            'aktif' => BookingFutsal::where('user_id', $userId)
                ->whereHas('booking', function ($query) {
                    $query->whereIn('status', ['menunggu', 'dikonfirmasi']);
                })->count(),
            'history' => BookingFutsal::where('user_id', $userId)
                ->whereHas('booking', function ($query) {
                    $query->whereIn('status', ['selesai', 'dibatalkan']);
                })->count(),
        ];

        // 2. Aktivitas Terbaru (5 records)
        $recent = BookingFutsal::with(['booking', 'lapangan'])
            ->where('user_id', $userId)
            ->whereHas('booking')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('user.futsal.dashboard', compact('summary', 'recent'));
    }

    /**
     * Menampilkan halaman histori booking futsal
     */
    public function history(Request $request)
    {
        $userId = Auth::id();
        
        $query = BookingFutsal::with(['booking', 'lapangan'])
            ->where('user_id', $userId)
            ->whereHas('booking');

        // Apply Filters
        if ($request->status && $request->status !== 'all') {
            $query->whereHas('booking', function ($q) use ($request) {
                $q->where('status', $request->status);
            });
        }

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('start_datetime', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }

        $history = $query->orderBy('start_datetime', 'desc')->paginate(10);

        return view('user.futsal.history', compact('history'));
    }

    /**
     * API: Mendapatkan detail satu booking (Tetap digunakan untuk Modal)
     */
    public function getDetail($id)
    {
        $userId = Auth::id();
        $booking = BookingFutsal::with(['booking', 'lapangan', 'booking.pembayaranFutsal.tipePembayaran'])
            ->where('user_id', $userId)
            ->whereHas('booking')
            ->findOrFail($id);

        $pay = $booking->booking->pembayaranFutsal->first();

        return response()->json([
            'id' => $booking->id,
            'lapangan' => [
                'nama' => $booking->lapangan->nama ?? 'Lapangan Tidak Diketahui',
                'ukuran' => $booking->lapangan->ukuran ?? '-',
                'deskripsi' => $booking->lapangan->deskripsi ?? '',
            ],
            'jadwal' => [
                'tanggal' => $booking->type === 'event' 
                    ? \Carbon\Carbon::parse($booking->start_datetime)->format('d F Y') . ' - ' . \Carbon\Carbon::parse($booking->end_datetime)->format('d F Y')
                    : \Carbon\Carbon::parse($booking->start_datetime)->format('d F Y'),
                'jam' => $booking->type === 'event' 
                    ? '-' 
                    : $booking->start_datetime->format('H:i') . ' - ' . $booking->end_datetime->format('H:i'),
                'durasi' => $booking->type === 'event' 
                    ? $booking->durasi_hari . ' Hari' 
                    : $booking->durasi_jam . ' Jam',
            ],
            'status' => [
                'label' => ucfirst($booking->booking->status ?? 'Menunggu'),
                'color' => $this->getStatusColor($booking->booking->status ?? 'menunggu'),
            ],
            'pembayaran' => [
                'jenis' => ucfirst($booking->jenis_pembayaran ?? 'reguler'),
                'total' => 'Rp ' . number_format($pay->jumlah_bayar ?? 0, 0, ',', '.'),
                'status' => ucfirst($pay->status ?? 'Menunggu'),
                'metode' => $booking->jenis_pembayaran === 'membership' ? 'Membership (Potong Kuota)' : ($pay->tipePembayaran->nama ?? 'Tunai'),
            ]
        ]);
    }

    /**
     * Menampilkan Invoice versi HTML (untuk print browser)
     */
    public function showInvoice($id)
    {
        $userId = Auth::id();
        $booking = BookingFutsal::with(['booking', 'lapangan', 'booking.pembayaranFutsal.tipePembayaran', 'user'])
            ->where('user_id', $userId)
            ->whereHas('booking')
            ->findOrFail($id);

        $pembayaran = $booking->booking->pembayaranFutsal->first();
        $pengaturan = \App\Models\Pengaturan::first();

        return view('user.futsal.invoice', compact('booking', 'pembayaran', 'pengaturan'));
    }

    /**
     * Mengunduh Invoice PDF
     */
    public function downloadInvoice($id)
    {
        $userId = Auth::id();
        $booking = BookingFutsal::with(['booking', 'lapangan', 'booking.pembayaranFutsal.tipePembayaran', 'user'])
            ->where('user_id', $userId)
            ->whereHas('booking')
            ->findOrFail($id);

        $pembayaran = $booking->booking->pembayaranFutsal->first();
        $pengaturan = \App\Models\Pengaturan::first();

        $pdf = Pdf::loadView('user.futsal.invoice-pdf', compact('booking', 'pembayaran', 'pengaturan'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('Invoice-Futsal-' . $booking->id . '.pdf');
    }

    /**
     * Landing page sistem futsal untuk user/pelanggan
     */
    public function landing()
    {
        $lapangans = Lapangan::all();

        $paketMemberships = PaketMembership::where('status', 'aktif')->get();

        $membershipAktif = Membership::where('user_id', Auth::id())
            ->where('status', 'aktif')
            ->with('paket')
            ->first();

        return view('user.futsal.landing', compact('lapangans', 'paketMemberships', 'membershipAktif'));
    }

    /**
     * Menampilkan form pembelian membership
     */
    public function showMembershipForm()
    {
        $paketMemberships = PaketMembership::where('status', 'aktif')->get();
        $membershipAktif = Membership::where('user_id', Auth::id())
            ->where('status', 'aktif')
            ->with('paket')
            ->first();

        return view('user.futsal.membership', compact('paketMemberships', 'membershipAktif'));
    }

    /**
     * Proses pembelian membership
     */
    public function storeMembership(Request $request)
    {
        $request->validate([
            'paket_membership_id' => 'required|exists:paket_membership,id',
        ]);

        // Cek apakah user sudah punya membership aktif
        $membershipAktif = Membership::where('user_id', Auth::id())
            ->where('status', 'aktif')
            ->first();

        if ($membershipAktif) {
            return redirect()->back()->with('error', 'Anda sudah memiliki membership aktif.');
        }

        $paket = PaketMembership::findOrFail($request->paket_membership_id);

        Membership::create([
            'user_id'             => Auth::id(),
            'paket_membership_id' => $paket->id,
            'total_kuota'         => $paket->jumlah_kuota,
            'sisa_kuota'          => $paket->jumlah_kuota,
            'tgl_daftar'          => now()->toDateString(),
            'status'              => 'aktif',
        ]);

        return redirect()->route('user.futsal.membership.form')
            ->with('success', 'Membership berhasil dibeli! Selamat menikmati fasilitas futsal.');
    }

    /**
     * Menampilkan form booking lapangan
     */
    public function showBookingForm()
    {
        $userId = Auth::id();

        $lapangans = Lapangan::all();

        $paketMemberships = PaketMembership::where('status', 'aktif')->get();

        $membership = Membership::where('user_id', $userId)
            ->where('status', 'aktif')
            ->with('paket')
            ->first();

        return view('user.futsal.booking', compact('lapangans', 'paketMemberships', 'membership'));
    }

    /**
     * AJAX: Cek ketersediaan slot jadwal berdasarkan lapangan & tanggal
     */
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

        // --- Auto-Generate Jadwal Jika Belum Ada Berdasarkan Pengaturan ---
        if ($pengaturan) {
            $jamBukaSettings = Carbon::parse($jamBuka);
            $jamTutupSettings = Carbon::parse($jamTutup);
            $currentStart = $jamBukaSettings->copy();
            
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

    /**
     * Simpan booking baru dari user/pelanggan
     */
    public function store(Request $request)
    {
        $userId = Auth::id();

        $validated = $request->validate([
            'lapangan_id'      => 'required|exists:lapangan,id',
            'type'             => 'required|in:regular,event',
            'jenis_pembayaran' => 'required|in:reguler,membership',
            // Untuk regular
            'tanggal'          => 'nullable|required_if:type,regular|date|after_or_equal:today',
            'jam_mulai_id'     => 'nullable|required_if:type,regular|exists:jadwal_lapangan,id',
            'durasi_main'      => 'nullable|required_if:type,regular|integer|min:1|max:3',
            // Untuk event
            'start_datetime'   => 'nullable|required_if:type,event|date|after_or_equal:today',
            'end_datetime'     => 'nullable|required_if:type,event|date|after:start_datetime',
        ]);

        // --- FLOW REGULAR ---
        if ($validated['type'] === 'regular') {
            $slot = JadwalLapangan::where('id', $validated['jam_mulai_id'])
                ->where('lapangan_id', $validated['lapangan_id'])
                ->where('tanggal', $validated['tanggal'])
                ->where('status', 'tersedia')
                ->first();

            if (!$slot) {
                return back()->withInput()->with('error', 'Slot jadwal tidak tersedia.');
            }

            $startDatetime = Carbon::parse($validated['tanggal'] . ' ' . $slot->jam_mulai);
            $endDatetime   = $startDatetime->copy()->addHours((int) $validated['durasi_main']);
            
            // Cek overlap
            $overlap = BookingFutsal::where('lapangan_id', $validated['lapangan_id'])
            ->whereHas('booking', fn($q) => $q->whereNotIn('status', ['dibatalkan']))
            ->where(function ($q) use ($startDatetime, $endDatetime) {
                $q->where('start_datetime', '<', $endDatetime)
                ->where('end_datetime', '>', $startDatetime);
            })
            ->exists();

            if ($overlap) {
                return back()->withInput()->with('error', 'Waktu yang dipilih sudah bentrok dengan booking lain.');
            }

            // Cek membership
            $membership = null;
            if ($validated['jenis_pembayaran'] === 'membership') {
                $membership = Membership::where('user_id', $userId)
                    ->where('status', 'aktif')
                    ->first();

                if (!$membership) {
                    return back()->withInput()->with('error', 'Anda tidak memiliki membership aktif.');
                }

                $durasiJam = $startDatetime->diffInHours($endDatetime);
                if ($membership->sisa_kuota < $durasiJam) {
                    return back()->withInput()->with('error', 'Sisa kuota membership tidak mencukupi.');
                }
            }

            $isBookingMembershipPertama = false;
            if ($validated['jenis_pembayaran'] === 'membership') {
                $isBookingMembershipPertama = PembayaranFutsal::where('jenis_transaksi', 'membership')
                    ->whereHas('booking', fn($q) => $q->where('user_id', $userId))
                    ->where('jumlah_bayar', '>', 0)
                    ->doesntExist();
            }

            DB::transaction(function () use ($userId, $validated, $slot, $startDatetime, $endDatetime, $membership, $isBookingMembershipPertama) {
                $booking = Booking::create([
                    'user_id' => $userId,
                    'status'  => ($validated['jenis_pembayaran'] === 'membership' && !$isBookingMembershipPertama)
                        ? 'dikonfirmasi' : 'menunggu',
                ]);

                BookingFutsal::create([
                    'booking_id'       => $booking->id,
                    'user_id'          => $userId,
                    'lapangan_id'      => $validated['lapangan_id'],
                    'start_datetime'   => $startDatetime,
                    'end_datetime'     => $endDatetime,
                    'type'             => 'regular',
                    'jenis_pembayaran' => $validated['jenis_pembayaran'],
                ]);

                if ($validated['jenis_pembayaran'] === 'membership') {
                    $membership->load('paket');
                    PembayaranFutsal::create([
                        'booking_id'         => $booking->id,
                        'jenis_transaksi'    => 'membership',
                        'tipe_pembayaran_id' => 2,
                        'jumlah_bayar'       => $isBookingMembershipPertama ? $membership->paket->harga : 0,
                        'status'             => $isBookingMembershipPertama ? 'menunggu' : 'verifikasi',
                        'tgl_bayar'          => now(),
                    ]);
                    $durasiJam = $startDatetime->diffInHours($endDatetime);
                    $membership->gunakanKuota($durasiJam);
                } else {
                    PembayaranFutsal::create([
                        'booking_id'         => $booking->id,
                        'jenis_transaksi'    => 'booking',
                        'tipe_pembayaran_id' => 2,
                        'jumlah_bayar'       => 0,
                        'status'             => 'menunggu',
                        'tgl_bayar'          => now(),
                    ]);
                }
                $endJam = $endDatetime->format('H:i:s');
                JadwalLapangan::where('lapangan_id', $validated['lapangan_id'])
                    ->where('tanggal', $validated['tanggal'])
                    ->where('jam_mulai', '>=', $slot->jam_mulai)
                    ->where('jam_mulai', '<', $endJam)
                    ->update(['status' => 'terisi']);
            });
        }

        // --- FLOW EVENT ---
        if ($validated['type'] === 'event') {
            $startDatetime = Carbon::parse($validated['start_datetime']);
            $endDatetime   = Carbon::parse($validated['end_datetime']);

            // Cek overlap
            $overlap = BookingFutsal::where('lapangan_id', $validated['lapangan_id'])
                ->whereHas('booking', fn($q) => $q->whereNotIn('status', ['dibatalkan']))
                ->where(function ($q) use ($startDatetime, $endDatetime) {
                    $q->where('start_datetime', '<', $endDatetime)
                    ->where('end_datetime', '>', $startDatetime);
                })
                ->exists();

            if ($overlap) {
                return back()->withInput()->with('error', 'Waktu event bentrok dengan booking lain.');
            }

            DB::transaction(function () use ($userId, $validated, $startDatetime, $endDatetime) {
                $booking = Booking::create([
                    'user_id' => $userId,
                    'status'  => 'menunggu',
                ]);
                $durasiHari = $startDatetime->diffInDays($endDatetime);
                $durasiHari = max(1, $durasiHari);
                $hargaEvent = 800000 * $durasiHari;

                BookingFutsal::create([
                    'booking_id'       => $booking->id,
                    'user_id'          => $userId,
                    'lapangan_id'      => $validated['lapangan_id'],
                    'start_datetime'   => $startDatetime,
                    'end_datetime'     => $endDatetime,
                    'type'             => 'event',
                    'jenis_pembayaran' => 'reguler',
                ]);

                PembayaranFutsal::create([
                    'booking_id'         => $booking->id,
                    'jenis_transaksi'    => 'event',
                    'tipe_pembayaran_id' => 2,
                    'jumlah_bayar'       => $hargaEvent,
                    'status'             => 'menunggu',
                    'tgl_bayar'          => now(),
                ]);
            });
        }

        return redirect()->route('user.futsal.dashboard')
            ->with('success', 'Booking berhasil dibuat! Menunggu konfirmasi dari admin.');
    }

    private function getStatusColor($status)
    {
        return match (strtolower($status ?? '')) {
            'menunggu' => 'yellow',
            'dikonfirmasi' => 'blue',
            'selesai' => 'green',
            'dibatalkan' => 'red',
            default => 'gray',
        };
    }
}
