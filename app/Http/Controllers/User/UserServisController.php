<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\LayananServis;
use App\Models\BookingServis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UserServisController extends Controller
{
    /**
     * Tampilkan Dashboard Servis (Booking Aktif/Sedang Berjalan).
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = BookingServis::with(['rincianServis', 'pembayaranServis'])
            ->where('user_id', $user->id)
            ->whereIn('status', ['menunggu', 'diproses']);

        // Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search (Nomor Plat atau Tanggal)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_plat', 'like', "%{$search}%")
                    ->orWhere('tanggal_booking', 'like', "%{$search}%");
            });
        }

        $bookings = $query->latest('tanggal_booking')
            ->paginate(10)
            ->withQueryString();

        return view('user.servis.index', compact('bookings'));
    }

    /**
     * Tampilkan Katalog Layanan.
     */
    public function katalog()
    {
        $layanans = LayananServis::where('is_active', true)->get();
        return view('user.servis.katalog', compact('layanans'));
    }

    /**
     * Tampilkan Form Booking (Layanan di-select dari katalog).
     */
    public function booking(Request $request)
    {
        // Validasi: Harus pilih layanan dari katalog dulu
        if (!$request->has('layanan_id')) {
            return redirect()->route('user.servis.katalog')->with('error', 'Silakan pilih layanan dari katalog terlebih dahulu.');
        }

        $layananTerpilih = LayananServis::where('is_active', true)->findOrFail($request->layanan_id);

        return view('user.servis.booking', compact('layananTerpilih'));
    }

    /**
     * Simpan booking servis baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'layanan_servis_id' => 'required|exists:layanan_servis,id',
            'tipe_kendaraan' => 'required|in:motor,mobil',
            'merek_kendaraan'   => 'required|string|max:100',
            'nomor_plat'        => 'required|string|max:20',
            'tahun_kendaraan'   => 'nullable|digits:4|integer|min:1990|max:' . date('Y'),
            'keluhan'           => 'nullable|string|max:500',
            'tanggal_booking'   => 'required|date|after_or_equal:today',
            'jam_booking'       => 'required|in:' . implode(',', $this->generateJamSlot()),
        ]);

        // Validasi: tanggal + jam tidak boleh di masa lalu
        $waktuBooking = Carbon::parse($request->tanggal_booking . ' ' . $request->jam_booking);
        if ($waktuBooking->isPast()) {
            return back()
                ->withErrors(['jam_booking' => 'Waktu yang dipilih sudah lewat.'])
                ->withInput();
        }

        // Validasi slot via method model BookingServis::isSlotAvailable
        if (!BookingServis::isSlotAvailable($request->tanggal_booking, $request->jam_booking)) {
            return back()
                ->withErrors(['jam_booking' => 'Slot pada jam ini sudah penuh (Maks. 3).'])
                ->withInput();
        }

        // Simpan Data
        BookingServis::create([
            'user_id'           => Auth::id(),
            'layanan_servis_id' => $request->layanan_servis_id,
            'tipe_kendaraan'    => $request->tipe_kendaraan,
            'merek_kendaraan'   => $request->merek_kendaraan,
            'nomor_plat'        => strtoupper($request->nomor_plat),
            'tahun_kendaraan'   => $request->tahun_kendaraan,
            'keluhan'           => $request->keluhan,
            'tanggal_booking'   => $request->tanggal_booking,
            'jam_booking'       => $request->jam_booking,
            'status'            => 'menunggu',
        ]);

        return redirect()->route('user.servis.katalog')
            ->with('success', 'Booking berhasil dikirim! Silakan cek berkala status booking Anda.');
    }

    /**
     * Endpoint AJAX untuk cek ketersediaan slot jam per tanggal.
     */
    public function getSlot(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date|after_or_equal:today',
        ]);

        $tanggal = $request->tanggal;
        $jamTersedia = $this->generateJamSlot();

        // Hitung booking per jam (exclude status batal & selesai)
        $bookingPerJam = BookingServis::where('tanggal_booking', $tanggal)
            ->whereNotIn('status', ['batal', 'selesai'])
            ->selectRaw('jam_booking, COUNT(*) as total')
            ->groupBy('jam_booking')
            ->pluck('total', 'jam_booking')
            ->toArray();

        $slots = [];
        foreach ($jamTersedia as $jam) {
            $total = $bookingPerJam[$jam] ?? 0;
            $slots[] = [
                'jam'       => $jam,
                'terisi'    => (int)$total,
                'kapasitas' => 3,
                'tersedia'  => $total < 3,
            ];
        }

        return response()->json($slots);
    }

    /**
     * Tampilkan Histori Servis (Semua data).
     */
    public function history(Request $request)
    {
        $user = Auth::user();

        $query = BookingServis::with(['rincianServis', 'pembayaranServis', 'layananServis'])
            ->where('user_id', $user->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_plat', 'like', "%{$search}%")
                    ->orWhere('kode_booking', 'like', "%{$search}%");
            });
        }

        $bookings = $query->latest('tanggal_booking')->paginate(15)->withQueryString();

        return view('user.servis.history', compact('bookings'));
    }

    /**
     * Tampilkan Detail Booking.
     */
    public function show($id)
    {
        $booking = BookingServis::with(['rincianServis.produkServis', 'pembayaranServis', 'layananServis', 'teknisi'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('user.servis.show', compact('booking'));
    }

    /**
     * Helper: Jam operasional 08:00 - 16:00.
     */
    private function generateJamSlot(): array
    {
        $slots = [];
        for ($jam = 8; $jam <= 16; $jam++) {
            $slots[] = sprintf('%02d:00', $jam);
        }
        return $slots;
    }
}
