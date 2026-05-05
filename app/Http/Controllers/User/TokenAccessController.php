<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BookingFutsal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TokenAccessController extends Controller
{
    // SHOW — Tampilkan form OTP atau detail booking (jika session masih valid)
    public function show($token)
    {
        // Cek apakah ini token servis
        $bookingServis = \App\Models\BookingServis::where('access_token', $token)->first();
        if ($bookingServis) {
            return redirect()->route('user.servis.token.show', $token);
        }

        $bookingFutsal = BookingFutsal::with(['booking.pembayaranFutsal.tipePembayaran', 'lapangan'])
            ->where('access_token', $token)->firstOrFail();

        // Jika booking sudah selesai/ditolak/dibatalkan → link tidak aktif
        if (in_array($bookingFutsal->status, ['selesai', 'ditolak', 'dibatalkan'])) {
            return view('user.token.expired', ['status' => $bookingFutsal->status]);
        }

        // Cek session OTP masih valid (60 menit setelah verifikasi berhasil)
        $verifiedUntil = session('otp_verified_until_' . $token);
        if ($verifiedUntil && now()->lt(Carbon::parse($verifiedUntil))) {
            // Session masih valid → langsung tampilkan detail
            return view('user.token.detail', compact('bookingFutsal', 'token'));
        }

        // Session habis / belum pernah verifikasi → alur OTP
        // Cek apakah OTP yang ada masih berlaku (belum expired & tidak diblokir)
        $isBlocked  = $bookingFutsal->otp_blocked_until && now()->lt($bookingFutsal->otp_blocked_until);
        $isOtpValid = $bookingFutsal->otp_code
                      && $bookingFutsal->otp_expired_at
                      && now()->lt($bookingFutsal->otp_expired_at)
                      && !$isBlocked;

        if (!$isOtpValid && !$isBlocked) {
            // Generate OTP baru dan kirim via WA
            // (ini yang dimaksud ketua project: OTP dikirim saat buka link, bukan saat booking)
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $bookingFutsal->update([
                'otp_code'          => $otp,
                'otp_expired_at'    => now()->addMinutes(3),
                'otp_attempt'       => 0,
                'otp_sent_at'       => now(),
                'otp_blocked_until' => null,
            ]);
            $this->sendWhatsappOtp($bookingFutsal->no_hp, $otp);
        }
        // Jika OTP masih berlaku → tampilkan form tanpa kirim ulang
        // Jika diblokir → tampilkan form dengan banner blokir

        $canResend = $this->checkCanResend($bookingFutsal);

        return view('user.token.otp', compact('bookingFutsal', 'token', 'canResend'));
    }

    // VERIFY OTP — Cek kode yang diinput user
    public function verifyOtp(Request $request, $token)
    {
        $request->validate(['otp_input' => 'required|string|size:6']);

        $bookingFutsal = BookingFutsal::where('access_token', $token)->firstOrFail();

        // Cek blokir
        if ($bookingFutsal->otp_blocked_until && now()->lt($bookingFutsal->otp_blocked_until)) {
            $menitSisa = max(1, (int) now()->diffInMinutes($bookingFutsal->otp_blocked_until, false));
            return back()->with('error', "Terlalu banyak percobaan. Coba lagi dalam {$menitSisa} menit.");
        }

        // Cek kedaluwarsa
        if (!$bookingFutsal->otp_expired_at || now()->gt($bookingFutsal->otp_expired_at)) {
            return back()->with('error', "Kode OTP sudah kedaluwarsa. Klik 'Kirim Ulang'.");
        }

        // Bandingkan kode
        if ($request->otp_input !== $bookingFutsal->otp_code) {
            $attempt = $bookingFutsal->otp_attempt + 1;
            if ($attempt >= 3) {
                $bookingFutsal->update([
                    'otp_attempt'       => $attempt,
                    'otp_blocked_until' => now()->addMinutes(3),
                ]);
                return back()->with('error', 'Terlalu banyak percobaan. Akses diblokir selama 3 menit.');
            }
            $bookingFutsal->update(['otp_attempt' => $attempt]);
            $sisa = 3 - $attempt;
            return back()->with('error', "Kode OTP salah. Sisa {$sisa} percobaan.");
        }

        // OTP benar → bersihkan kode & simpan session
        $bookingFutsal->update([
            'otp_code'          => null,
            'otp_attempt'       => 0,
            'otp_blocked_until' => null,
        ]);

        session([
            'otp_verified_' . $token       => true,
            'otp_verified_until_' . $token => now()->addMinutes(60)->toDateTimeString(),
        ]);

        return redirect()->route('user.token.show', $token);
    }

    // RESEND OTP — Kirim ulang dengan cooldown 60 detik
    public function resendOtp(Request $request, $token)
    {
        $bookingFutsal = BookingFutsal::where('access_token', $token)->firstOrFail();

        // Cek masih diblokir
        if ($bookingFutsal->otp_blocked_until && now()->lt($bookingFutsal->otp_blocked_until)) {
            $menitSisa = max(1, (int) now()->diffInMinutes($bookingFutsal->otp_blocked_until, false));
            return back()->with('error', "Masih diblokir. Coba lagi dalam {$menitSisa} menit.");
        }

        // Cek cooldown 60 detik
        if ($bookingFutsal->otp_sent_at) {
            $detikSejak  = now()->diffInSeconds($bookingFutsal->otp_sent_at);
            $detikTunggu = 60 - (int) $detikSejak;
            if ($detikTunggu > 0) {
                return back()->with('error', "Tunggu {$detikTunggu} detik sebelum mengirim ulang.");
            }
        }

        // Generate OTP baru
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $bookingFutsal->update([
            'otp_code'          => $otp,
            'otp_expired_at'    => now()->addMinutes(3),
            'otp_attempt'       => 0,
            'otp_blocked_until' => null,
            'otp_sent_at'       => now(),
        ]);
        $this->sendWhatsappOtp($bookingFutsal->no_hp, $otp);

        return back()->with('success', 'Kode OTP baru telah dikirim ke WhatsApp Anda.');
    }

    // BATALKAN — Konfirmasi pembatalan booking
    public function batalkan($token)
    {
        $bookingServis = \App\Models\BookingServis::where('access_token', $token)->first();
        if ($bookingServis) {
            return redirect()->route('user.servis.token.batalkan', $token);
        }

        $bookingFutsal = BookingFutsal::with(['booking', 'lapangan'])
            ->where('access_token', $token)->firstOrFail();

        $canCancel = $bookingFutsal->status === 'menunggu'
                     && Carbon::parse($bookingFutsal->start_datetime)->gt(now()->addHours(2));

        if (!$canCancel) {
            return redirect()->route('user.token.show', $token)
                ->with('error', 'Booking tidak bisa dibatalkan karena sudah dikonfirmasi atau waktu bermain kurang dari 2 jam.');
        }

        return view('user.token.batalkan', compact('bookingFutsal', 'token'));
    }

    public function prosesBatalkan(Request $request, $token)
    {
        $bookingFutsal = BookingFutsal::with(['booking'])
            ->where('access_token', $token)->firstOrFail();

        $canCancel = $bookingFutsal->status === 'menunggu'
                     && Carbon::parse($bookingFutsal->start_datetime)->gt(now()->addHours(2));

        if (!$canCancel) {
            return redirect()->route('user.token.show', $token)
                ->with('error', 'Booking tidak memenuhi syarat untuk dibatalkan.');
        }

        $bookingFutsal->update(['status' => 'dibatalkan']);
        if ($bookingFutsal->booking) {
            $bookingFutsal->booking->update(['status' => 'dibatalkan']);
        }

        return redirect()->route('user.token.show', $token)
            ->with('success', 'Booking berhasil dibatalkan.');
    }

    // HELPER: Kirim OTP via WhatsApp (Fonnte)
    private function sendWhatsappOtp(string $noHp, string $otpCode): void
    {
        $apiToken   = config('services.fonnte.token');
        $noHpBersih = preg_replace('/[^0-9]/', '', $noHp);

        $pesan  = "🔐 *Kode OTP Booking Futsal BLUD*\n\n";
        $pesan .= "Kode verifikasi Anda: *{$otpCode}*\n\n";
        $pesan .= "Kode ini berlaku selama *3 menit*.\n";
        $pesan .= "Jangan bagikan kode ini kepada siapapun.\n\n";
        $pesan .= "_Jika Anda tidak merasa melakukan booking, abaikan pesan ini._";

        try {
            Http::withHeaders(['Authorization' => $apiToken])
                ->asForm()
                ->post('https://api.fonnte.com/send', [
                    'target'      => $noHpBersih,
                    'message'     => $pesan,
                    'countryCode' => '62',
                    'token'       => $apiToken,
                ]);
        } catch (\Exception $e) {
            Log::warning("Gagal kirim OTP WA ke {$noHpBersih}: " . $e->getMessage());
        }

        Log::info("OTP {$otpCode} dikirim ke {$noHpBersih}");
    }

    // HELPER: Cek apakah tombol resend boleh aktif
    private function checkCanResend(BookingFutsal $booking): bool
    {
        if ($booking->otp_blocked_until && now()->lt($booking->otp_blocked_until)) {
            return false;
        }
        if ($booking->otp_sent_at) {
            $detikSejak = now()->diffInSeconds($booking->otp_sent_at);
            if ($detikSejak < 60) {
                return false;
            }
        }
        return true;
    }
}