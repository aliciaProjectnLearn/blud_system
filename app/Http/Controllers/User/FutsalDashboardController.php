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
            $query->whereBetween('tgl_main', [$request->start_date, $request->end_date]);
        }

        $history = $query->orderBy('tgl_main', 'desc')->paginate(10);

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
                'tanggal' => Carbon::parse($booking->tgl_main)->format('d F Y'),
                'jam' => substr($booking->jam_mulai, 0, 5) . ' - ' . substr($booking->jam_selesai, 0, 5),
                'durasi' => $booking->durasi_main . ' Jam',
            ],
            'status' => [
                'label' => ucfirst($booking->booking->status ?? 'Menunggu'),
                'color' => $this->getStatusColor($booking->booking->status ?? 'menunggu'),
            ],
            'pembayaran' => [
                'jenis' => ucfirst($booking->jenis_pembayaran ?? 'reguler'),
                'total' => 'Rp ' . number_format($pay->jumlah_bayar ?? 0, 0, ',', '.'),
                'status' => ucfirst($pay->status ?? 'Menunggu'),
                'metode' => $pay->tipePembayaran->nama ?? 'Tunai',
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

        $slots = JadwalLapangan::where('lapangan_id', $request->lapangan_id)
            ->where('tanggal', $request->tanggal)
            ->where('status', 'tersedia')
            ->orderBy('jam_mulai')
            ->get(['id', 'jam_mulai', 'jam_selesai', 'status']);

        return response()->json([
            'success' => true,
            'slots'   => $slots,
        ]);
    }

    /**
     * Simpan booking baru dari user/pelanggan
     */
/**
 * Simpan booking baru dari user/pelanggan
 */
public function store(Request $request)
{
    $userId = Auth::id();

    $validated = $request->validate([
        'lapangan_id'      => 'required|exists:lapangan,id',
        'tanggal'          => 'required|date|after_or_equal:today',
        'jam_mulai_id'     => 'required|exists:jadwal_lapangan,id',
        'durasi_main'      => 'required|integer|min:1|max:3',
        'jenis_pembayaran' => 'required|in:reguler,membership',
    ]);

    $slot = JadwalLapangan::where('id', $validated['jam_mulai_id'])
        ->where('lapangan_id', $validated['lapangan_id'])
        ->where('tanggal', $validated['tanggal'])
        ->where('status', 'tersedia')
        ->first();

    if (!$slot) {
        return back()->withInput()->with('error', 'Slot jadwal yang dipilih tidak tersedia atau sudah terisi.');
    }

    $jamMulai   = Carbon::parse($slot->jam_mulai);
    $jamSelesai = $jamMulai->copy()->addHours((int) $validated['durasi_main']);

    $overlap = BookingFutsal::where('lapangan_id', $validated['lapangan_id'])
        ->where('tgl_main', $validated['tanggal'])
        ->whereHas('booking', fn($q) => $q->whereNotIn('status', ['dibatalkan']))
        ->where(function ($q) use ($jamMulai, $jamSelesai) {
            $q->where(function ($inner) use ($jamMulai, $jamSelesai) {
                $inner->where('jam_mulai', '<', $jamSelesai->format('H:i:s'))
                      ->where('jam_selesai', '>', $jamMulai->format('H:i:s'));
            });
        })
        ->exists();

    if ($overlap) {
        return back()->withInput()->with('error', 'Waktu yang dipilih sudah bentrok dengan booking lain.');
    }

    $membership = null;
    if ($validated['jenis_pembayaran'] === 'membership') {
        $membership = Membership::where('user_id', $userId)
            ->where('status', 'aktif')
            ->first();

        if (!$membership) {
            return back()->withInput()->with('error', 'Anda tidak memiliki membership aktif.');
        }

        if ($membership->sisa_kuota < (int) $validated['durasi_main']) {
            return back()->withInput()->with('error', 'Sisa kuota membership tidak mencukupi untuk durasi yang dipilih.');
        }
    }

    // ← Pindahkan pengecekan ke sini, sebelum DB::transaction
    $isBookingMembershipPertama = false;
    if ($validated['jenis_pembayaran'] === 'membership') {
        $isBookingMembershipPertama = PembayaranFutsal::where('jenis_transaksi', 'membership')
            ->whereHas('booking', function($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->where('jumlah_bayar', '>', 0)
            ->doesntExist();
    }

    DB::transaction(function () use ($userId, $validated, $slot, $jamMulai, $jamSelesai, $membership, $isBookingMembershipPertama) {

        // 1. Buat record booking
        $booking = Booking::create([
            'user_id' => $userId,
            'status'  => ($validated['jenis_pembayaran'] === 'membership' && !$isBookingMembershipPertama)
                ? 'dikonfirmasi'
                : 'menunggu',
        ]);

        // 2. Buat record booking_futsal
        BookingFutsal::create([
            'booking_id'        => $booking->id,
            'user_id'           => $userId,
            'lapangan_id'       => $validated['lapangan_id'],
            'tgl_main'          => $validated['tanggal'],
            'jam_mulai'         => $jamMulai->format('H:i:s'),
            'jam_mulai_efektif' => $jamMulai->format('H:i:s'),
            'jam_selesai'       => $jamSelesai->format('H:i:s'),
            'durasi_main'       => $validated['durasi_main'],
            'jenis_pembayaran'  => $validated['jenis_pembayaran'],
        ]);

        // 3. Buat record pembayaran_futsal
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

        // 4. Kurangi kuota membership
        if ($membership) {
            $membership->gunakanKuota((int) $validated['durasi_main']);
        }

        // 5. Update status jadwal lapangan
        $slot->update(['status' => 'terisi']);
    });

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
