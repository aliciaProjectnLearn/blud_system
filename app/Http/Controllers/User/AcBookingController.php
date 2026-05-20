<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingAc;
use App\Models\LayananAc;
use App\Models\User;
use App\Models\Role;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AcBookingController extends Controller
{
    public function index(Request $request)
    {
        $statusFilter = $request->status;
        $layanans = LayananAc::all();
        
        // Query Dasar
        $query = BookingAc::with(['layanan', 'booking', 'pembayaran']);
        
        // Filter Berdasarkan Auth (Jika login) atau Redirect (Jika guest)
        if (!auth()->check()) {
            return redirect()->route('user.ac.layanan');
        }

        $query = BookingAc::with(['layanan', 'booking', 'pembayaran']);
        $query->where('user_id', auth()->id());
        
        // Filter Status
        if ($statusFilter && in_array($statusFilter, ['menunggu', 'proses', 'selesai', 'canceled'])) {
            if ($statusFilter === 'canceled') {
                $query->whereHas('booking', function($q) {
                    $q->where('status', 'dibatalkan');
                });
            } else {
                $query->where('status', $statusFilter);
            }
        }
        
        $bookings = $query->latest()->paginate(10);
        
        // Hitung Stats
        $statsQuery = BookingAc::query();
        if (auth()->check()) {
            $statsQuery->where('user_id', auth()->id());
        } else {
            $statsQuery->whereRaw('1 = 0');
        }
        
        $stats = [
            'total'   => (clone $statsQuery)->count(),
            'pending' => (clone $statsQuery)->where('status', 'menunggu')->count(),
            'proses'  => (clone $statsQuery)->where('status', 'proses')->count(),
            'selesai' => (clone $statsQuery)->where('status', 'selesai')->count(),
        ];

        return view('user.ac.index', compact('layanans', 'bookings', 'stats', 'statusFilter'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'            => 'required|string|max:255',
            'no_hp'           => 'required|string|max:20',
            'layanan_id'      => 'required|exists:layanan_ac,id',
            'tgl_kunjungan'   => 'required|date|after_or_equal:today',
            'alamat'          => 'required|string|max:255',
            'merek_ac'        => 'nullable|string|max:100',
            'detail_keluhan'  => 'nullable|string',
        ]);

        // [1] PEMBATASAN BOOKING AKTIF
        $activeBooking = BookingAc::where('no_hp', $validated['no_hp'])
            ->whereIn('status', ['menunggu', 'proses'])
            ->exists();

        if ($activeBooking) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda masih memiliki booking yang sedang aktif. Selesaikan atau batalkan terlebih dahulu sebelum membuat booking baru.',
                ], 422);
            }
            return back()->withInput()->with('error', 'Anda masih memiliki booking yang sedang aktif. Selesaikan atau batalkan terlebih dahulu sebelum membuat booking baru.');
        }

        $accessToken = Str::random(40);

        DB::beginTransaction();
        try {
            // Kita tetap buat entri di tabel booking untuk sinkronisasi sistem
            $booking = Booking::create([
                'user_id' => auth()->id(), // null jika tidak login
                'status'  => 'menunggu',
                'access_token' => $accessToken,
            ]);

            $bookingAc = BookingAc::create([
                'booking_id'      => $booking->id,
                'user_id'         => auth()->id(),
                'nama_pelanggan'  => $validated['nama'],
                'no_hp'           => $validated['no_hp'],
                'layanan_id'      => $validated['layanan_id'],
                'tgl_kunjungan'   => $validated['tgl_kunjungan'],
                'alamat'          => $validated['alamat'],
                'merek_ac'        => $validated['merek_ac'],
                'detail_keluhan'  => $validated['detail_keluhan'],
                'status'          => 'menunggu',
                'access_token'    => $accessToken,
            ]);

            // 🚀 Kirim WhatsApp via Fonnte
            $this->sendBookingNotification($bookingAc);

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Booking AC berhasil dibuat! Link akses status pesanan telah dikirim ke WhatsApp Anda.',
                ]);
            }

            return redirect()->route('user.ac.layanan')->with('success', 'Booking AC berhasil dibuat! Link akses status pesanan telah dikirim ke WhatsApp Anda.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    private function sendBookingNotification($booking)
    {
        $apiToken = config('services.fonnte.token');
        if (!$apiToken || $apiToken === 'YOUR_API_TOKEN_HERE') {
            \Illuminate\Support\Facades\Log::warning("Fonnte Token tidak ditemukan atau masih default di .env. Notifikasi WA tidak terkirim.");
            return;
        }

        $link = route('user.ac.token.show', $booking->access_token);
        $layananNama = $booking->layanan->nama ?? 'Servis AC';
        
        $pesan = "*BOOKING SERVIS AC BERHASIL!* ✅\n\n";
        $pesan .= "Halo {$booking->nama_pelanggan}, pesanan Anda telah kami terima.\n\n";
        $pesan .= "🔧 *Layanan*: {$layananNama}\n";
        $pesan .= "📅 *Rencana Kunjungan*: " . \Carbon\Carbon::parse($booking->tgl_kunjungan)->translatedFormat('d F Y') . "\n";
        $pesan .= "📍 *Alamat*: {$booking->alamat}\n\n";
        $pesan .= "Simpan link berikut untuk memantau status servis Anda:\n";
        $pesan .= "🔗 {$link}\n\n";
        $pesan .= "Terima kasih telah menggunakan layanan kami.";

        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => $apiToken,
            ])->post('https://api.fonnte.com/send', [
                'target' => $booking->no_hp,
                'message' => $pesan,
                'countryCode' => '62',
            ]);
            
            \Illuminate\Support\Facades\Log::info("Respon Fonnte: " . $response->body());
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Fonnte Error: " . $e->getMessage());
        }
    }

    public function layanan()
    {
        $layanans = LayananAc::with('kategori')->paginate(12);
        
        // Ambil semua kategori yang terkait dengan Layanan AC
        $kategoris = Kategori::whereHas('layananAc')->get();

        return view('user.ac.layanan', compact('layanans', 'kategoris'));
    }
}
