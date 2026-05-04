<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\LayananServis;
use App\Models\BookingServis;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Str;

class UserServisController extends Controller
{
    /**
     * Redirect to Katalog Layanan.
     */
    public function index()
    {
        return redirect()->route('user.servis.katalog');
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
     * Tampilkan Form Booking.
     */
    public function booking(Request $request)
    {
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
            'nama'              => 'required|string|max:255',
            'no_hp'             => 'required|string|max:20',
            'layanan_servis_id' => 'required|exists:layanan_servis,id',
            'merek_kendaraan'   => 'required|string|max:100',
            'model_kendaraan'   => 'required|string|max:100',
            'nomor_plat'        => 'required|string|max:20',
            'tahun_kendaraan'   => 'required|digits:4|integer|min:1990|max:' . date('Y'),
            'keluhan'           => 'nullable|string|max:500',
            'tanggal_booking'   => 'required|date|after_or_equal:today',
            'jam_booking'       => 'required|in:' . implode(',', $this->generateJamSlot()),
        ]);

        $waktuBooking = Carbon::parse($request->tanggal_booking . ' ' . $request->jam_booking);
        if ($waktuBooking->isPast()) {
            return back()->withErrors(['jam_booking' => 'Waktu yang dipilih sudah lewat.'])->withInput();
        }

        if (!BookingServis::isSlotAvailable($request->tanggal_booking, $request->jam_booking)) {
            return back()->withErrors(['jam_booking' => 'Slot pada jam ini sudah penuh (Maks. 3).'])->withInput();
        }

        $layanan = LayananServis::findOrFail($request->layanan_servis_id);

        // Find or create user
        $user = User::where('no_hp', $request->no_hp)->first();
        if (!$user) {
            $user = User::create([
                'name' => $request->nama,
                'username' => $request->nama,
                'no_hp' => $request->no_hp,
                // Kolom esensial lain dibiarkan nullable sesuai DB terbaru
                'password' => bcrypt(Str::random(16)),
            ]);
            
            $role = Role::where('nama', 'pelanggan')->first();
            if ($role) {
                $user->roles()->attach($role->id);
            }
        }

        // Generate Token Unik 64 Karakter
        do {
            $accessToken = Str::random(64);
        } while (BookingServis::where('access_token', $accessToken)->exists());

        // Buat Kode Booking
        $kodeBooking = strtoupper(substr($layanan->tipe_kendaraan, 0, 3)) . '-' . strtoupper(Str::random(6));

        $booking = BookingServis::create([
            'kode_booking'      => $kodeBooking,
            'user_id'           => $user->id,
            'nama_pemesan'      => $request->nama,
            'no_hp'             => $request->no_hp,
            'layanan_servis_id' => $request->layanan_servis_id,
            'merek_kendaraan'   => $request->merek_kendaraan . ' ' . $request->model_kendaraan, // Gabung karena di DB tidak ada kolom model
            'nomor_plat'        => strtoupper($request->nomor_plat),
            'tahun_kendaraan'   => $request->tahun_kendaraan,
            'keluhan'           => $request->keluhan,
            'tanggal_booking'   => $request->tanggal_booking,
            'jam_booking'       => $request->jam_booking,
            'status'            => 'menunggu',
            'access_token'      => $accessToken,
        ]);

        // Kirim Notifikasi WhatsApp via Fonnte
        $fonnteToken = env('FONNTE_TOKEN');
        if ($fonnteToken) {
            $linkAkses = route('user.servis.token.show', $accessToken);
            $tanggalFormat = Carbon::parse($request->tanggal_booking)->translatedFormat('d F Y');
            
            $pesanWa = "Yth. Bapak/Ibu {$request->nama},\n\n"
                . "Terima kasih telah menggunakan layanan Sistem Servis Kendaraan di BLUD SMKN 1 Cirebon. Booking servis Anda telah berhasil dicatat dengan rincian sebagai berikut:\n\n"
                . "Kode Booking: *{$kodeBooking}*\n"
                . "Kendaraan: {$request->merek_kendaraan} {$request->model_kendaraan} ({$request->tahun_kendaraan})\n"
                . "Layanan: {$layanan->nama_layanan}\n"
                . "Jadwal: {$tanggalFormat} pukul {$request->jam_booking} WIB\n\n"
                . "Untuk memantau status pengerjaan kendaraan dan detail riwayat servis Anda, silakan akses tautan resmi berikut:\n"
                . "{$linkAkses}\n\n"
                . "Harap simpan tautan di atas dengan baik. Tautan tersebut bersifat rahasia dan merupakan kunci akses Anda ke dalam sistem kami.\n\n"
                . "Hormat kami,\n"
                . "*Sistem Servis - BLUD SMKN 1 Cirebon*";

            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => $fonnteToken
            ])->post('https://api.fonnte.com/send', [
                'target' => $request->no_hp,
                'message' => $pesanWa,
                'countryCode' => '62',
            ]);

            \Illuminate\Support\Facades\Log::info('Fonnte Response: ' . $response->body());
        }

        return redirect()->route('home')->with([
            'booking_success' => true,
            'no_hp' => $request->no_hp
        ]);
    }

    public function sukses($token)
    {
        $booking = BookingServis::where('access_token', $token)->firstOrFail();
        
        $linkAkses = route('user.servis.token.show', $token);
        
        // Buat URL wa.me
        $pesanWa = "Halo, ini adalah link akses untuk melihat status servis kendaraan saya di BLUD SMK:\n" . $linkAkses . "\nMohon bantuannya ya, terima kasih!";
        $urlWa = "https://wa.me/?text=" . urlencode($pesanWa);

        return view('user.servis.sukses', compact('booking', 'linkAkses', 'urlWa'));
    }

    /**
     * Tampilkan halaman detail booking publik.
     */
    public function detailToken($token)
    {
        $booking = BookingServis::with(['layananServis', 'rincianServis', 'pembayaranServis', 'fotoServis'])
            ->where('access_token', $token)
            ->first();

        if (!$booking) {
            return response()->view('user.servis.error_token', [], 404);
        }

        // Cek session dengan expiry time
        $verifiedUntil = session('otp_verified_until_' . $token);
        if ($verifiedUntil && now()->lt(\Carbon\Carbon::parse($verifiedUntil))) {
            return view('user.servis.detail_token', compact('booking'));
        }

        // Cek apakah OTP masih diblokir
        $blockedUntil = \Illuminate\Support\Facades\Cache::get('booking_servis_otp_blocked_' . $token);
        if ($blockedUntil && now()->lt(\Carbon\Carbon::parse($blockedUntil))) {
            return view('user.servis.otp', compact('booking', 'token'));
        }

        // Cek apakah OTP masih berlaku
        $otpCode = \Illuminate\Support\Facades\Cache::get('booking_servis_otp_' . $token);
        $otpExpiredAt = \Illuminate\Support\Facades\Cache::get('booking_servis_otp_expired_' . $token);

        if (!$otpCode || !$otpExpiredAt || now()->gt(\Carbon\Carbon::parse($otpExpiredAt))) {
            // Generate OTP baru
            $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $otpExpiredAt = now()->addMinutes(3)->toDateTimeString();

            \Illuminate\Support\Facades\Cache::put('booking_servis_otp_' . $token, $otpCode, now()->addMinutes(3));
            \Illuminate\Support\Facades\Cache::put('booking_servis_otp_expired_' . $token, $otpExpiredAt, now()->addMinutes(3));
            \Illuminate\Support\Facades\Cache::put('booking_servis_otp_attempt_' . $token, 0, now()->addMinutes(3));
            \Illuminate\Support\Facades\Cache::put('booking_servis_otp_sent_at_' . $token, now()->toDateTimeString(), now()->addMinutes(3));

            $this->sendWhatsappOtp($booking->no_hp, $otpCode);
        }

        return view('user.servis.otp', compact('booking', 'token'));
    }

    /**
     * Verifikasi OTP untuk servis kendaraan.
     */
    public function verifyOtp(Request $request, $token)
    {
        $request->validate(['otp_input' => 'required|string|size:6']);

        $booking = BookingServis::where('access_token', $token)->firstOrFail();

        $blockedUntil = \Illuminate\Support\Facades\Cache::get('booking_servis_otp_blocked_' . $token);
        if ($blockedUntil && now()->lt(\Carbon\Carbon::parse($blockedUntil))) {
            $menitSisa = max(1, now()->diffInMinutes(\Carbon\Carbon::parse($blockedUntil), false));
            return back()->with('error', "Terlalu banyak percobaan. Coba lagi dalam {$menitSisa} menit.");
        }

        $otpExpiredAt = \Illuminate\Support\Facades\Cache::get('booking_servis_otp_expired_' . $token);
        if (!$otpExpiredAt || now()->gt(\Carbon\Carbon::parse($otpExpiredAt))) {
            return back()->with('error', "Kode OTP sudah kedaluwarsa. Klik 'Kirim Ulang'.");
        }

        $otpCode = \Illuminate\Support\Facades\Cache::get('booking_servis_otp_' . $token);
        if ($request->otp_input !== $otpCode) {
            $attempt = (int)\Illuminate\Support\Facades\Cache::get('booking_servis_otp_attempt_' . $token, 0) + 1;
            \Illuminate\Support\Facades\Cache::put('booking_servis_otp_attempt_' . $token, $attempt, now()->addMinutes(3));

            if ($attempt >= 3) {
                \Illuminate\Support\Facades\Cache::put('booking_servis_otp_blocked_' . $token, now()->addMinutes(3)->toDateTimeString(), now()->addMinutes(3));
                return back()->with('error', 'Terlalu banyak percobaan. Akses diblokir selama 3 menit.');
            }

            $sisa = 3 - $attempt;
            return back()->with('error', "Kode OTP salah. Sisa {$sisa} percobaan.");
        }

        // OTP Valid! Bersihkan cache dan simpan di session
        \Illuminate\Support\Facades\Cache::forget('booking_servis_otp_' . $token);
        \Illuminate\Support\Facades\Cache::forget('booking_servis_otp_expired_' . $token);
        \Illuminate\Support\Facades\Cache::forget('booking_servis_otp_attempt_' . $token);
        \Illuminate\Support\Facades\Cache::forget('booking_servis_otp_blocked_' . $token);

        session([
            'otp_verified_' . $token => true,
            'otp_verified_until_' . $token => now()->addMinutes(60)->toDateTimeString(),
        ]);

        return redirect()->route('user.servis.token.show', $token);
    }

    /**
     * Kirim ulang OTP untuk servis kendaraan.
     */
    public function resendOtp(Request $request, $token)
    {
        $booking = BookingServis::where('access_token', $token)->firstOrFail();

        $blockedUntil = \Illuminate\Support\Facades\Cache::get('booking_servis_otp_blocked_' . $token);
        if ($blockedUntil && now()->lt(\Carbon\Carbon::parse($blockedUntil))) {
            $menitSisa = max(1, now()->diffInMinutes(\Carbon\Carbon::parse($blockedUntil), false));
            return back()->with('error', "Masih diblokir. Coba lagi dalam {$menitSisa} menit.");
        }

        $otpSentAt = \Illuminate\Support\Facades\Cache::get('booking_servis_otp_sent_at_' . $token);
        if ($otpSentAt) {
            $detikSejak = now()->diffInSeconds(\Carbon\Carbon::parse($otpSentAt));
            $detikTunggu = 60 - (int) $detikSejak;
            if ($detikTunggu > 0) {
                return back()->with('error', "Tunggu {$detikTunggu} detik sebelum mengirim ulang.");
            }
        }

        // Generate OTP baru
        $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $otpExpiredAt = now()->addMinutes(3)->toDateTimeString();

        \Illuminate\Support\Facades\Cache::put('booking_servis_otp_' . $token, $otpCode, now()->addMinutes(3));
        \Illuminate\Support\Facades\Cache::put('booking_servis_otp_expired_' . $token, $otpExpiredAt, now()->addMinutes(3));
        \Illuminate\Support\Facades\Cache::put('booking_servis_otp_attempt_' . $token, 0, now()->addMinutes(3));
        \Illuminate\Support\Facades\Cache::put('booking_servis_otp_sent_at_' . $token, now()->toDateTimeString(), now()->addMinutes(3));

        $this->sendWhatsappOtp($booking->no_hp, $otpCode);

        return back()->with('success', 'Kode OTP baru telah dikirim ke WhatsApp Anda.');
    }

    /**
     * Helper untuk mengirim OTP ke WhatsApp (Fonnte).
     */
    private function sendWhatsappOtp(string $noHp, string $otpCode): void
    {
        $apiToken = env('FONNTE_TOKEN');
        if (!$apiToken) {
            $apiToken = config('services.fonnte.token');
        }
        $noHpBersih = preg_replace('/[^0-9]/', '', $noHp);

        $pesan  = "🔐 *Kode OTP Booking Servis BLUD*\n\n";
        $pesan .= "Kode verifikasi Anda: *{$otpCode}*\n\n";
        $pesan .= "Kode ini berlaku selama *3 menit*.\n";
        $pesan .= "Jangan bagikan kode ini kepada siapapun.\n\n";
        $pesan .= "_Jika Anda tidak merasa melakukan booking, abaikan pesan ini._";

        try {
            \Illuminate\Support\Facades\Http::withHeaders(['Authorization' => $apiToken])
                ->asForm()
                ->post('https://api.fonnte.com/send', [
                    'target'      => $noHpBersih,
                    'message'     => $pesan,
                    'countryCode' => '62',
                ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Gagal kirim OTP WA ke {$noHpBersih}: " . $e->getMessage());
        }
    }

    /**
     * Batalkan booking servis (hanya status menunggu).
     */
    public function batalkan($token)
    {
        $booking = BookingServis::where('access_token', $token)->firstOrFail();

        if (strtolower($booking->status) !== 'menunggu') {
            return redirect()->route('user.servis.token.show', $token)
                ->with('error', 'Booking tidak bisa dibatalkan karena status sudah bukan menunggu.');
        }

        $booking->update(['status' => 'batal']);

        return redirect()->route('user.servis.token.show', $token)
            ->with('success', 'Booking berhasil dibatalkan.');
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
        $sekarang = \Carbon\Carbon::now('Asia/Jakarta');
        $isHariIni = ($tanggal === $sekarang->toDateString());

        $bookingPerJam = BookingServis::where('tanggal_booking', $tanggal)
            ->whereNotIn('status', ['batal', 'selesai'])
            ->selectRaw('jam_booking, COUNT(*) as total')
            ->groupBy('jam_booking')
            ->pluck('total', 'jam_booking')
            ->toArray();

        $slots = [];
        foreach ($jamTersedia as $jam) {
            $total = $bookingPerJam[$jam] ?? 0;
            $kapasitasPenuh = $total >= 3;

            // Cek apakah jam sudah lewat (hanya untuk hari ini)
            $sudahLewat = false;
            if ($isHariIni) {
                $jamInt = (int) substr($jam, 0, 2);
                // Jam dianggap tidak bisa dipilih jika jam sekarang
                // sudah sama atau melewati jam slot tersebut
                $sudahLewat = $sekarang->hour >= $jamInt;
            }

            $slots[] = [
                'jam'        => $jam,
                'terisi'     => (int) $total,
                'kapasitas'  => 3,
                'tersedia'   => !$kapasitasPenuh && !$sudahLewat,
                'sudah_lewat'=> $sudahLewat,
            ];
        }

        // Cek apakah semua slot hari ini sudah lewat atau penuh
        $adaYangTersedia = collect($slots)->where('tersedia', true)->count() > 0;

        return response()->json([
            'slots'             => $slots,
            'ada_yang_tersedia' => $adaYangTersedia,
            'tanggal'           => $tanggal,
            'is_hari_ini'       => $isHariIni,
        ]);
    }

    private function generateJamSlot(): array
    {
        $slots = [];
        for ($jam = 8; $jam <= 16; $jam++) {
            $slots[] = sprintf('%02d:00', $jam);
        }
        return $slots;
    }
}
