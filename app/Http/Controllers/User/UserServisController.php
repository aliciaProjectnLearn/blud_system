<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\LayananServis;
use App\Models\BookingServis;
use App\Models\User;
use App\Models\Role;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Str;

class UserServisController extends Controller
{
    protected OtpService $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

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

    public function booking(Request $request)
    {
        if (!$request->has('layanan_id')) {
            return redirect()->route('user.servis.katalog')->with('error', 'Silakan pilih layanan dari katalog terlebih dahulu.');
        }

        $layananTerpilih = LayananServis::where('is_active', true)->findOrFail($request->layanan_id);
        $mereks = \App\Models\MerekKendaraan::where('is_active', true)
            ->when($layananTerpilih->tipe_kendaraan, function($query, $tipe) {
                return $query->where('tipe', $tipe);
            })
            ->orderBy('nama')
            ->get();

        // Cek apakah ada booking aktif (berdasarkan no_hp jika dikirim via query param)
        $bookingAktif = null;
        if ($request->filled('no_hp')) {
            $bookingAktif = BookingServis::where('no_hp', $request->no_hp)
                ->whereIn('status', ['menunggu', 'diproses', 'siap_bayar'])
                ->first();
        }

        return view('user.servis.booking', compact('layananTerpilih', 'bookingAktif', 'mereks'));
    }


    /**
     * Simpan booking servis baru.
     * REVISI 1: Cek booking ganda sebelum menyimpan.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama'               => 'required|string|max:255',
            'no_hp'              => 'required|string|max:20',
            'layanan_servis_id'  => 'required|exists:layanan_servis,id',
            'merek_kendaraan_id' => 'required|exists:merek_kendaraan,id',
            'model_kendaraan_id' => 'required|exists:model_kendaraan,id',
            'nomor_plat'         => 'required|string|max:20',
            'tahun_kendaraan'    => 'required|digits:4|integer|min:1990|max:' . date('Y'),
            'keluhan'            => 'nullable|string|max:500',
            'tanggal_booking'    => 'required|date|after_or_equal:today',
            'jam_booking'        => 'required|in:' . implode(',', $this->generateJamSlot()),
        ]);

        // Cek apakah model milik merek yang dipilih
        $modelExists = \App\Models\ModelKendaraan::where('id', $request->model_kendaraan_id)
            ->where('merek_kendaraan_id', $request->merek_kendaraan_id)
            ->exists();
        if (!$modelExists) {
            return back()->withErrors(['model_kendaraan_id' => 'Model kendaraan tidak sesuai dengan merek yang dipilih.'])->withInput();
        }

        // ── REVISI 1: Cek booking ganda ──────────────────────────────────────────
        $bookingAktif = BookingServis::where('no_hp', $request->no_hp)
            ->whereIn('status', ['menunggu', 'diproses', 'siap_bayar'])
            ->first();

        if ($bookingAktif) {
            return back()->withErrors([
                'no_hp' => 'Kamu masih memiliki booking servis yang sedang diproses (Kode: ' . $bookingAktif->kode_booking . ', Status: ' . strtoupper($bookingAktif->status) . '). Selesaikan booking tersebut terlebih dahulu.',
            ])->withInput();
        }
        // ─────────────────────────────────────────────────────────────────────────

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
                'name'     => $request->nama,
                'username' => $request->nama,
                'no_hp'    => $request->no_hp,
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
        $kodeBooking = strtoupper(substr($layanan->tipe_kendaraan ?? 'SRV', 0, 3)) . '-' . strtoupper(Str::random(6));

        $merek = \App\Models\MerekKendaraan::findOrFail($request->merek_kendaraan_id);
        $model = \App\Models\ModelKendaraan::findOrFail($request->model_kendaraan_id);

        $booking = BookingServis::create([
            'kode_booking'      => $kodeBooking,
            'user_id'           => $user->id,
            'nama_pemesan'      => $request->nama,
            'no_hp'             => $request->no_hp,
            'layanan_servis_id' => $request->layanan_servis_id,
            'merek_kendaraan'   => $merek->nama . ' ' . $model->nama_model,
            'nomor_plat'        => strtoupper($request->nomor_plat),
            'tahun_kendaraan'   => $request->tahun_kendaraan,
            'keluhan'           => $request->keluhan,
            'tanggal_booking'   => $request->tanggal_booking,
            'jam_booking'       => $request->jam_booking,
            'status'            => 'menunggu',
            'access_token'      => $accessToken,
        ]);

        // ── Kirim Notifikasi WhatsApp via Fonnte ──────────────────────────────────
        // BUGFIX: gunakan route servis, bukan futsal
        $linkAkses = route('user.servis.token.show', $accessToken);
        $fonnteToken = env('FONNTE_TOKEN');

        if ($fonnteToken) {
            $tanggalFormat = Carbon::parse($request->tanggal_booking)->translatedFormat('d F Y');

            $pesanWa = "Yth. Bapak/Ibu {$request->nama},\n\n"
                . "Terima kasih telah menggunakan layanan Sistem Servis Kendaraan di BLUD SMKN 1 Cirebon. Booking servis Anda telah berhasil dicatat dengan rincian sebagai berikut:\n\n"
                . "Kode Booking: *{$kodeBooking}*\n"
                . "Kendaraan: {$merek->nama} {$model->nama_model} ({$request->tahun_kendaraan})\n"
                . "Layanan: {$layanan->nama_layanan}\n"
                . "Jadwal: {$tanggalFormat} pukul {$request->jam_booking} WIB\n\n"
                . "Untuk memantau status pengerjaan kendaraan dan detail riwayat servis Anda, silakan akses tautan resmi berikut:\n"
                . "{$linkAkses}\n\n"
                . "Harap simpan tautan di atas dengan baik. Tautan tersebut bersifat rahasia dan merupakan kunci akses Anda ke dalam sistem kami.\n\n"
                . "Hormat kami,\n"
                . "*Sistem Servis - BLUD SMKN 1 Cirebon*";

            $response = Http::withHeaders([
                'Authorization' => $fonnteToken,
            ])->post('https://api.fonnte.com/send', [
                'target'      => $request->no_hp,
                'message'     => $pesanWa,
                'countryCode' => '62',
            ]);

            Log::info('Fonnte Response: ' . $response->body());
        }
        // ─────────────────────────────────────────────────────────────────────────

        return redirect()->route('home')->with([
            'booking_success' => true,
            'no_hp'           => $request->no_hp,
        ]);
    }

    /**
     * Halaman sukses setelah booking.
     */
    public function sukses($token)
    {
        $booking = BookingServis::where('access_token', $token)->firstOrFail();

        // Link mengarah ke entry point OTP (bukan detail langsung)
        $linkAkses = route('user.servis.token.show', $token);

        $pesanWa = "Halo, ini adalah link akses untuk melihat status servis kendaraan saya di BLUD SMK:\n" . $linkAkses . "\nMohon bantuannya ya, terima kasih!";
        $urlWa   = "https://wa.me/?text=" . urlencode($pesanWa);

        return view('user.servis.sukses', compact('booking', 'linkAkses', 'urlWa'));
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // REVISI 3 — OTP SYSTEM
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Entry point akses token servis.
     * Cek session → kirim OTP → tampilkan form verifikasi.
     */
    public function showToken($token)
    {
        $booking = BookingServis::where('access_token', $token)->first();

        if (!$booking) {
            return response()->view('user.servis.error_token', [], 404);
        }

        if ($booking->status === 'selesai') {
            return response()->view('user.servis.selesai_token', [], 403);
        }

        // Jika session masih valid, langsung ke detail
        if ($this->otpService->isSessionValid($token)) {
            return $this->renderDetail($booking, $token);
        }


        // Ambil status OTP saat ini
        $otpStatus = $this->otpService->getOtpStatus($booking);

        // Jika tidak diblokir dan boleh kirim (belum pernah kirim atau expired),
        // auto-kirim OTP
        if (!$otpStatus['blocked'] && (!$otpStatus['has_otp'] || $otpStatus['otp_expired'])) {
            $resendCheck = $this->otpService->canResend($booking);
            if ($resendCheck['can_resend']) {
                $otpCode = $this->otpService->generateAndSave($booking);
                $this->otpService->sendViaWhatsapp($booking, $otpCode);
                $booking->refresh();
                $otpStatus = $this->otpService->getOtpStatus($booking);
            }
        }

        $maskedPhone = $this->otpService->maskPhone($booking->no_hp ?? '');

        return view('user.servis.otp_verify', compact('booking', 'token', 'otpStatus', 'maskedPhone'));
    }

    /**
     * Proses verifikasi OTP yang disubmit user.
     */
    public function verifyOtp(Request $request, $token)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $booking = BookingServis::where('access_token', $token)->first();

        if (!$booking) {
            return redirect()->route('user.servis.katalog')->with('error', 'Token tidak valid.');
        }

        $result = $this->otpService->verify($booking, $request->otp);

        if ($result['success']) {
            $this->otpService->setSession($token);
            return redirect()->route('user.servis.token.show', $token)
                ->with('otp_success', 'Verifikasi berhasil!');
        }

        return back()->withErrors(['otp' => $result['message']])->withInput();
    }

    /**
     * Kirim ulang OTP (AJAX atau redirect).
     */
    public function resendOtp(Request $request, $token)
    {
        $booking = BookingServis::where('access_token', $token)->first();

        if (!$booking) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Token tidak valid.'], 404);
            }
            return redirect()->route('user.servis.katalog')->with('error', 'Token tidak valid.');
        }

        $resendCheck = $this->otpService->canResend($booking);

        if (!$resendCheck['can_resend']) {
            $msg = $resendCheck['reason'] === 'blocked'
                ? "Akun masih diblokir. Tunggu {$resendCheck['cooldown_seconds']} detik."
                : "Harap tunggu {$resendCheck['cooldown_seconds']} detik sebelum mengirim ulang.";

            if ($request->wantsJson()) {
                return response()->json([
                    'success'          => false,
                    'message'          => $msg,
                    'cooldown_seconds' => $resendCheck['cooldown_seconds'],
                ]);
            }
            return back()->with('error', $msg);
        }

        $otpCode = $this->otpService->generateAndSave($booking);
        $sent    = $this->otpService->sendViaWhatsapp($booking, $otpCode);

        if ($request->wantsJson()) {
            return response()->json([
                'success'          => true,
                'message'          => $sent
                    ? 'OTP telah dikirim ulang ke WhatsApp Anda.'
                    : 'OTP di-generate tapi gagal dikirim. Hubungi admin jika berlanjut.',
                'cooldown_seconds' => \App\Services\OtpService::RESEND_COOLDOWN_SECONDS,
            ]);
        }

        return back()->with('info', $sent
            ? 'OTP telah dikirim ulang ke WhatsApp Anda.'
            : 'Gagal mengirim OTP. Silakan coba lagi.');
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // HALAMAN DETAIL TOKEN SERVIS (setelah OTP berhasil)
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Tampilkan halaman detail booking publik (dipanggil internal setelah OTP).
     * Juga bisa diakses langsung jika session masih valid.
     */
    public function detailToken($token)
    {
        $booking = BookingServis::with(['layananServis', 'rincianServis', 'pembayaranServis', 'fotoServis'])
            ->where('access_token', $token)
            ->first();

        if (!$booking) {
            return response()->view('user.servis.error_token', [], 404);
        }

        if ($booking->status === 'selesai') {
            return response()->view('user.servis.selesai_token', [], 403);
        }

        // Guard: pastikan sudah OTP atau session valid
        if (!$this->otpService->isSessionValid($token)) {
            return redirect()->route('user.servis.token.show', $token);
        }

        return $this->renderDetail($booking, $token);
    }

    /**
     * Internal render detail (dipakai oleh showToken dan detailToken).
     */
    protected function renderDetail(BookingServis $booking, string $token)
    {
        // Eager load jika belum
        $booking->loadMissing(['layananServis', 'rincianServis', 'pembayaranServis', 'fotoServis']);

        return view('user.servis.detail_token', compact('booking', 'token'));
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // BATALKAN BOOKING SERVIS (via token)
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Halaman konfirmasi batalkan booking servis.
     */
    public function batalkanToken($token)
    {
        $booking = BookingServis::where('access_token', $token)->first();

        if (!$booking) {
            return response()->view('user.servis.error_token', [], 404);
        }

        // Guard session OTP
        if (!$this->otpService->isSessionValid($token)) {
            return redirect()->route('user.servis.token.show', $token);
        }

        if ($booking->status !== 'menunggu') {
            return redirect()->route('user.servis.token.detail', $token)
                ->with('error', 'Booking tidak dapat dibatalkan karena sudah diproses atau selesai.');
        }

        return view('user.servis.batalkan_token', compact('booking', 'token'));
    }

    /**
     * Proses pembatalan booking servis.
     */
    public function prosesBatalkanToken(Request $request, $token)
    {
        $booking = BookingServis::where('access_token', $token)->first();

        if (!$booking) {
            return response()->view('user.servis.error_token', [], 404);
        }

        // Guard session OTP
        if (!$this->otpService->isSessionValid($token)) {
            return redirect()->route('user.servis.token.show', $token);
        }

        if ($booking->status !== 'menunggu') {
            return redirect()->route('user.servis.token.detail', $token)
                ->with('error', 'Booking tidak memenuhi syarat untuk dibatalkan.');
        }

        $booking->update(['status' => 'batal']);

        return redirect()->route('user.servis.token.detail', $token)
            ->with('success', 'Booking berhasil dibatalkan.');
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // SLOT API & HELPERS
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Endpoint AJAX untuk cek ketersediaan slot jam per tanggal.
     */
    public function getSlot(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date|after_or_equal:today',
        ]);

        $tanggal    = $request->tanggal;
        $jamTersedia = $this->generateJamSlot();
        $sekarang   = Carbon::now('Asia/Jakarta');
        $isHariIni  = ($tanggal === $sekarang->toDateString());

        $bookingPerJam = BookingServis::where('tanggal_booking', $tanggal)
            ->whereNotIn('status', ['batal', 'selesai'])
            ->selectRaw('jam_booking, COUNT(*) as total')
            ->groupBy('jam_booking')
            ->pluck('total', 'jam_booking')
            ->toArray();

        $slots = [];
        foreach ($jamTersedia as $jam) {
            $total          = $bookingPerJam[$jam] ?? 0;
            $kapasitasPenuh = $total >= 3;

            $sudahLewat = false;
            if ($isHariIni) {
                $jamInt     = (int) substr($jam, 0, 2);
                $sudahLewat = $sekarang->hour >= $jamInt;
            }

            $slots[] = [
                'jam'         => $jam,
                'terisi'      => (int) $total,
                'kapasitas'   => 3,
                'tersedia'    => !$kapasitasPenuh && !$sudahLewat,
                'sudah_lewat' => $sudahLewat,
            ];
        }

        $adaYangTersedia = collect($slots)->where('tersedia', true)->count() > 0;

        return response()->json([
            'slots'             => $slots,
            'ada_yang_tersedia' => $adaYangTersedia,
            'tanggal'           => $tanggal,
            'is_hari_ini'       => $isHariIni,
        ]);
    }

    public function getModelByMerek($merek_id)
    {
        $models = \App\Models\ModelKendaraan::where('merek_kendaraan_id', $merek_id)
            ->where('is_active', true)
            ->orderBy('nama_model')
            ->get(['id', 'nama_model']);

        return response()->json($models);
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

