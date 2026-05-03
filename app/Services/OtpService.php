<?php

namespace App\Services;

use App\Models\BookingServis;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class OtpService
{
    /**
     * Durasi berlaku OTP (menit)
     */
    const OTP_LIFETIME_MINUTES = 3;

    /**
     * Cooldown kirim ulang (detik)
     */
    const RESEND_COOLDOWN_SECONDS = 60;

    /**
     * Maksimal percobaan sebelum diblokir
     */
    const MAX_ATTEMPTS = 3;

    /**
     * Durasi blokir setelah melebihi maksimal percobaan (menit)
     */
    const BLOCK_DURATION_MINUTES = 3;

    /**
     * Durasi session akses setelah OTP berhasil (menit)
     */
    const SESSION_LIFETIME_MINUTES = 60;

    /**
     * Cek apakah session akses token masih valid.
     */
    public function isSessionValid(string $token): bool
    {
        $sessionKey = 'servis_token_access_' . $token;
        $expiresAt = session($sessionKey);

        if (!$expiresAt) {
            return false;
        }

        if (Carbon::now()->isAfter(Carbon::parse($expiresAt))) {
            session()->forget($sessionKey);
            return false;
        }

        return true;
    }

    /**
     * Set session akses setelah OTP berhasil diverifikasi.
     */
    public function setSession(string $token): void
    {
        $sessionKey = 'servis_token_access_' . $token;
        session([$sessionKey => Carbon::now()->addMinutes(self::SESSION_LIFETIME_MINUTES)->toISOString()]);
    }

    /**
     * Hapus session akses (misalnya saat logout manual).
     */
    public function clearSession(string $token): void
    {
        session()->forget('servis_token_access_' . $token);
    }

    /**
     * Cek status OTP saat ini pada booking.
     * Return array info status.
     */
    public function getOtpStatus(BookingServis $booking): array
    {
        $now = Carbon::now();

        // Cek blokir
        if ($booking->otp_blocked_until && $now->isBefore(Carbon::parse($booking->otp_blocked_until))) {
            $secondsLeft = $now->diffInSeconds(Carbon::parse($booking->otp_blocked_until));
            return [
                'blocked'          => true,
                'block_seconds'    => $secondsLeft,
                'otp_expired'      => true,
                'can_resend'       => false,
                'resend_cooldown'  => 0,
                'has_otp'          => false,
            ];
        }

        $otpExpired = true;
        if ($booking->otp_expires_at && !$booking->otp_used) {
            $otpExpired = $now->isAfter(Carbon::parse($booking->otp_expires_at));
        }

        // Cek cooldown resend
        $canResend = true;
        $resendCooldown = 0;
        if ($booking->otp_sent_at) {
            $sentAt = Carbon::parse($booking->otp_sent_at);
            $elapsedSeconds = $sentAt->diffInSeconds($now);
            if ($elapsedSeconds < self::RESEND_COOLDOWN_SECONDS) {
                $canResend = $otpExpired; // Jika expired, bisa resend meski dalam cooldown
                $resendCooldown = self::RESEND_COOLDOWN_SECONDS - $elapsedSeconds;
            }
        }

        $otpSecondsLeft = 0;
        if (!$otpExpired && $booking->otp_expires_at) {
            $otpSecondsLeft = $now->diffInSeconds(Carbon::parse($booking->otp_expires_at));
        }

        return [
            'blocked'         => false,
            'block_seconds'   => 0,
            'otp_expired'     => $otpExpired,
            'can_resend'      => $canResend,
            'resend_cooldown' => $resendCooldown,
            'has_otp'         => !empty($booking->otp_code),
            'otp_seconds'     => $otpSecondsLeft,
        ];
    }

    /**
     * Generate kode OTP baru dan simpan ke booking.
     * Jika OTP masih valid, kembalikan kode yang sama.
     * Jika sudah expired atau belum ada, generate baru.
     */
    public function generateAndSave(BookingServis $booking): string
    {
        $now = Carbon::now();

        // Jika OTP masih valid dan belum dipakai, gunakan kode yang sama
        if (
            $booking->otp_code
            && !$booking->otp_used
            && $booking->otp_expires_at
            && $now->isBefore(Carbon::parse($booking->otp_expires_at))
        ) {
            // Update sent_at untuk reset cooldown resend
            DB::transaction(function () use ($booking, $now) {
                $booking->otp_sent_at = $now;
                $booking->save();
            });
            return $booking->otp_code;
        }

        // Generate OTP baru
        $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        DB::transaction(function () use ($booking, $otpCode, $now) {
            $booking->otp_code          = $otpCode;
            $booking->otp_expires_at    = $now->copy()->addMinutes(self::OTP_LIFETIME_MINUTES);
            $booking->otp_used          = false;
            $booking->otp_attempt_count = 0;
            $booking->otp_blocked_until = null;
            $booking->otp_sent_at       = $now;
            $booking->save();
        });

        return $otpCode;
    }

    /**
     * Kirim OTP via Fonnte WhatsApp API.
     */
    public function sendViaWhatsapp(BookingServis $booking, string $otpCode): bool
    {
        $phone = $booking->no_hp;
        if (!$phone) {
            Log::warning("OTP: no_hp kosong untuk booking #{$booking->id}");
            return false;
        }

        $fonnteToken = env('FONNTE_TOKEN');
        if (!$fonnteToken) {
            Log::warning('OTP: FONNTE_TOKEN tidak terkonfigurasi di .env');
            return false;
        }

        $maskedPhone = $this->maskPhone($phone);
        $pesan = "🔐 *Kode OTP Servis Kendaraan BLUD SMKN 1 Cirebon*\n\n"
            . "Kode OTP Anda: *{$otpCode}*\n\n"
            . "Kode berlaku selama *3 menit* sejak pesan ini diterima.\n"
            . "Jangan bagikan kode ini kepada siapapun.\n\n"
            . "_Sistem Servis - BLUD SMKN 1 Cirebon_";

        try {
            $response = Http::withHeaders([
                'Authorization' => $fonnteToken,
            ])->post('https://api.fonnte.com/send', [
                'target'      => $phone,
                'message'     => $pesan,
                'countryCode' => '62',
            ]);

            Log::info("OTP Fonnte Response untuk {$maskedPhone}: " . $response->body());
            return $response->successful();
        } catch (\Exception $e) {
            Log::error("OTP: Gagal kirim ke {$maskedPhone} — " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verifikasi OTP yang diinput user.
     * Return array: ['success', 'message', 'blocked', 'block_seconds', 'attempts_left']
     */
    public function verify(BookingServis $booking, string $inputOtp): array
    {
        $now = Carbon::now();

        // Cek apakah sedang diblokir
        if ($booking->otp_blocked_until && $now->isBefore(Carbon::parse($booking->otp_blocked_until))) {
            $secondsLeft = $now->diffInSeconds(Carbon::parse($booking->otp_blocked_until));
            return [
                'success'       => false,
                'message'       => "Terlalu banyak percobaan. Coba lagi dalam {$secondsLeft} detik.",
                'blocked'       => true,
                'block_seconds' => $secondsLeft,
                'attempts_left' => 0,
            ];
        }

        // Cek apakah OTP sudah dipakai
        if ($booking->otp_used) {
            return [
                'success'       => false,
                'message'       => 'OTP sudah pernah digunakan. Minta OTP baru.',
                'blocked'       => false,
                'block_seconds' => 0,
                'attempts_left' => 0,
            ];
        }

        // Cek apakah OTP expired
        if (!$booking->otp_expires_at || $now->isAfter(Carbon::parse($booking->otp_expires_at))) {
            return [
                'success'       => false,
                'message'       => 'OTP sudah kedaluwarsa. Klik "Kirim Ulang" untuk mendapatkan kode baru.',
                'blocked'       => false,
                'block_seconds' => 0,
                'attempts_left' => self::MAX_ATTEMPTS,
            ];
        }

        // Verifikasi kode
        if (trim($inputOtp) !== $booking->otp_code) {
            $newAttemptCount = $booking->otp_attempt_count + 1;
            $attemptsLeft = self::MAX_ATTEMPTS - $newAttemptCount;

            DB::transaction(function () use ($booking, $newAttemptCount, $attemptsLeft, $now) {
                $booking->otp_attempt_count = $newAttemptCount;

                if ($attemptsLeft <= 0) {
                    $booking->otp_blocked_until = $now->copy()->addMinutes(self::BLOCK_DURATION_MINUTES);
                }

                $booking->save();
            });

            if ($attemptsLeft <= 0) {
                return [
                    'success'       => false,
                    'message'       => 'Terlalu banyak percobaan. Akun diblokir selama ' . self::BLOCK_DURATION_MINUTES . ' menit.',
                    'blocked'       => true,
                    'block_seconds' => self::BLOCK_DURATION_MINUTES * 60,
                    'attempts_left' => 0,
                ];
            }

            return [
                'success'       => false,
                'message'       => "Kode OTP salah. Sisa percobaan: {$attemptsLeft}.",
                'blocked'       => false,
                'block_seconds' => 0,
                'attempts_left' => $attemptsLeft,
            ];
        }

        // OTP benar — tandai sebagai used
        DB::transaction(function () use ($booking) {
            $booking->otp_used          = true;
            $booking->otp_attempt_count = 0;
            $booking->otp_blocked_until = null;
            $booking->save();
        });

        return [
            'success'       => true,
            'message'       => 'OTP berhasil diverifikasi.',
            'blocked'       => false,
            'block_seconds' => 0,
            'attempts_left' => self::MAX_ATTEMPTS,
        ];
    }

    /**
     * Cek apakah boleh mengirim ulang OTP.
     * Return array: ['can_resend', 'reason', 'cooldown_seconds']
     */
    public function canResend(BookingServis $booking): array
    {
        $now = Carbon::now();

        // Jika diblokir, tidak bisa resend sampai blokir selesai
        if ($booking->otp_blocked_until && $now->isBefore(Carbon::parse($booking->otp_blocked_until))) {
            $secondsLeft = $now->diffInSeconds(Carbon::parse($booking->otp_blocked_until));
            return [
                'can_resend'       => false,
                'reason'           => 'blocked',
                'cooldown_seconds' => $secondsLeft,
            ];
        }

        // Jika belum pernah kirim, boleh
        if (!$booking->otp_sent_at) {
            return ['can_resend' => true, 'reason' => 'ok', 'cooldown_seconds' => 0];
        }

        $sentAt = Carbon::parse($booking->otp_sent_at);
        $elapsed = $sentAt->diffInSeconds($now);

        // Jika OTP masih valid, harus tunggu cooldown
        $otpExpired = !$booking->otp_expires_at || $now->isAfter(Carbon::parse($booking->otp_expires_at));

        if (!$otpExpired && $elapsed < self::RESEND_COOLDOWN_SECONDS) {
            return [
                'can_resend'       => false,
                'reason'           => 'cooldown',
                'cooldown_seconds' => self::RESEND_COOLDOWN_SECONDS - $elapsed,
            ];
        }

        return ['can_resend' => true, 'reason' => 'ok', 'cooldown_seconds' => 0];
    }

    /**
     * Mask nomor HP untuk keamanan tampilan.
     * Contoh: 081234567890 → 0812***7890
     */
    public function maskPhone(string $phone): string
    {
        $len = strlen($phone);
        if ($len <= 7) {
            return str_repeat('*', $len);
        }
        return substr($phone, 0, 4) . str_repeat('*', $len - 7) . substr($phone, -3);
    }
}
