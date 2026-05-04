<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingAc;
use App\Models\BookingFutsal;
use App\Models\BookingServis;
use App\Models\SewaRuko;
use App\Models\CekBookingLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CekBookingController extends Controller
{
    /**
     * Kirim OTP ke WhatsApp via Fonnte.
     */
    public function requestOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
        ]);

        $phone = $request->phone;
        
        // Clean phone number: remove non-digits
        $target = preg_replace('/[^0-9]/', '', $phone);
        
        // Consistent Normalization Pattern (as used in working controllers)
        if (str_starts_with($target, '0')) {
            $target = '62' . substr($target, 1);
        } elseif (!str_starts_with($target, '62')) {
            $target = '62' . $target;
        }

        // Validate format (628... length 11-14)
        if (!preg_match('/^628[0-9]{8,11}$/', $target)) {
            return response()->json([
                'success' => false,
                'message' => 'Format nomor WhatsApp tidak valid. Gunakan format 08... atau 628...'
            ], 422);
        }

        $blockedKey = "cek_otp_blocked:{$target}";
        $cooldownKey = "cek_otp_cooldown:{$target}";
        $otpKey = "cek_otp:{$target}";

        // Check if blocked
        if (Cache::has($blockedKey)) {
            $seconds = Cache::remainingSeconds($blockedKey);
            $minutes = ceil($seconds / 60);
            return response()->json([
                'success' => false,
                'message' => "Nomor Anda diblokir sementara karena terlalu banyak percobaan. Coba lagi dalam {$minutes} menit."
            ], 429);
        }

        // Check cooldown
        if (Cache::has($cooldownKey)) {
            return response()->json([
                'success' => false,
                'message' => 'Harap tunggu sebelum meminta OTP baru.'
            ], 429);
        }

        // Generate or retrieve OTP
        $otpData = Cache::get($otpKey);
        if ($otpData) {
            $otp = $otpData['otp_raw'];
        } else {
            $otp = rand(100000, 999999);
            Cache::put($otpKey, [
                'hashed' => Hash::make($otp),
                'otp_raw' => $otp, // Raw stored for resend as per requirement
            ], now()->addMinutes(3));
        }

        // Set cooldown
        Cache::put($cooldownKey, true, now()->addSeconds(60));

        // Send via Fonnte
        $this->sendOtpToWhatsapp($target, $otp);

        return response()->json([
            'success' => true,
            'message' => 'OTP telah dikirim ke WhatsApp Anda.'
        ]);
    }

    /**
     * Verifikasi OTP dan kirim link booking jika ditemukan.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'otp' => 'required|digits:6',
        ]);

        $phone = preg_replace('/[^0-9]/', '', $request->phone);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        } elseif (!str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }

        $blockedKey = "cek_otp_blocked:{$phone}";
        $attemptsKey = "cek_otp_attempts:{$phone}";
        $otpKey = "cek_otp:{$phone}";

        if (Cache::has($blockedKey)) {
            return response()->json([
                'success' => false,
                'message' => 'Terlalu banyak percobaan. Silakan coba lagi nanti.'
            ], 429);
        }

        $otpData = Cache::get($otpKey);
        if (!$otpData) {
            return response()->json([
                'success' => false,
                'message' => 'Kode OTP sudah kedaluwarsa. Silakan minta OTP baru.'
            ], 422);
        }

        if (Hash::check($request->otp, $otpData['hashed'])) {
            // Success: Invalidate OTP & Reset attempts
            Cache::forget($otpKey);
            Cache::forget($attemptsKey);
            Cache::forget("cek_otp_cooldown:{$phone}");

            // Find bookings
            $bookings = $this->findAllBookingsByPhone($phone);

            if ($bookings->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'found' => false,
                    'message' => 'Tidak ditemukan data booking aktif untuk nomor ini.'
                ]);
            }

            // Send Links
            $this->sendBookingLinksToWhatsapp($phone, $bookings);

            return response()->json([
                'success' => true,
                'found' => true,
                'message' => 'Link booking telah dikirim ke WhatsApp Anda.'
            ]);
        } else {
            // Wrong OTP
            $attempts = (int) Cache::get($attemptsKey, 0) + 1;
            Cache::put($attemptsKey, $attempts, now()->addMinutes(3));

            if ($attempts >= 3) {
                Cache::put($blockedKey, true, now()->addMinutes(3));
                Cache::forget($attemptsKey);
                return response()->json([
                    'success' => false,
                    'message' => 'Terlalu banyak percobaan yang salah. Nomor Anda diblokir selama 3 menit.'
                ], 429);
            }

            $remaining = 3 - $attempts;
            return response()->json([
                'success' => false,
                'message' => "Kode OTP salah. Kesempatan tersisa: {$remaining} dari 3.",
                'remaining_attempts' => $remaining
            ], 422);
        }
    }

    /**
     * Send OTP via Fonnte.
     */
    private function sendOtpToWhatsapp($target, $otp)
    {
        $apiToken = config('services.fonnte.token');
        $message = "Kode OTP Cek Booking Anda: *{$otp}*. Berlaku 3 menit. Jangan berikan kode ini kepada siapapun.";

        try {
            $response = Http::withHeaders([
                'Authorization' => $apiToken,
            ])->post('https://api.fonnte.com/send', [
                'target'      => $target,
                'message'     => $message,
                'countryCode' => '62',
            ]);

            $resBody = $response->json();
            if ($response->failed() || !($resBody['status'] ?? false)) {
                Log::warning('Fonnte API Error (Cek Booking OTP): ' . ($resBody['reason'] ?? $response->body()));
            }
        } catch (\Exception $e) {
            Log::error('Fonnte Connection Error (Cek Booking OTP): ' . $e->getMessage());
        }
    }

    /**
     * Send Booking Links via Fonnte.
     */
    private function sendBookingLinksToWhatsapp($target, $bookings)
    {
        $apiToken = config('services.fonnte.token');
        
        $message = "Halo! Berikut link booking Anda:\n\n";
        
        foreach ($bookings as $index => $b) {
            $num = $index + 1;
            $unit = $this->getUnitName($b);
            
            // Get status from booking or parent booking safely
            $status = 'N/A';
            if ($b instanceof SewaRuko) {
                $status = $b->status_sewa;
            } elseif ($b instanceof BookingServis || $b instanceof Booking) {
                $status = $b->status;
            } elseif (isset($b->booking)) {
                $status = $b->booking->status;
            }
            
            $statusText = ucfirst($status);
            $link = url('sewa-token/' . $b->access_token);
            
            $message .= "{$num}. {$unit} - Status: {$statusText}\n";
            $message .= "🔗 {$link}\n\n";
        }
        
        $message .= "Jika Anda merasa tidak melakukan booking ini, abaikan pesan ini.";

        try {
            $response = Http::withHeaders([
                'Authorization' => $apiToken,
            ])->post('https://api.fonnte.com/send', [
                'target'      => $target,
                'message'     => $message,
                'countryCode' => '62',
            ]);

            if ($response->failed()) {
                Log::warning('Fonnte API Error (Cek Booking Links): ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('Fonnte Connection Error (Cek Booking Links): ' . $e->getMessage());
        }
    }

    /**
     * Search all booking models for ACTIVE bookings.
     */
    private function findAllBookingsByPhone($phone)
    {
        // Define models and their ACTUAL phone fields from migrations
        // Tables without phone columns will rely on user relation
        $models = [
            BookingFutsal::class => [],
            BookingAc::class => [],
            BookingServis::class => ['no_hp'],
            SewaRuko::class => ['no_hp_snapshot'],
            Booking::class => [],
        ];

        $results = collect();

        foreach ($models as $modelClass => $fields) {
            $query = $modelClass::query();
            
            // Eager loading
            $query->with(['user']);
            
            // Check if model has booking relation before loading
            if ($modelClass !== Booking::class && method_exists(new $modelClass, 'booking')) {
                $query->with('booking');
            }

            // Eager load related units for better descriptive names
            if ($modelClass === SewaRuko::class) {
                $query->with('ruko');
            } elseif ($modelClass === BookingServis::class) {
                $query->with('layananServis');
            }
            
            // Check direct fields or via User relationship
            $query->where(function($q) use ($fields, $phone, $modelClass) {
                // 1. Direct phone columns (only if they exist in table)
                foreach ($fields as $field) {
                    $q->orWhere($field, $phone);
                    if (str_starts_with($phone, '62')) {
                        $q->orWhere($field, '0' . substr($phone, 2));
                    }
                }
                
                // 2. Via User relationship
                // Only if model has user relationship
                if (method_exists(new $modelClass, 'user')) {
                    $q->orWhereHas('user', function($qu) use ($phone) {
                        $qu->where('no_hp', $phone);
                        if (str_starts_with($phone, '62')) {
                            $qu->orWhere('no_hp', '0' . substr($phone, 2));
                        }
                    });
                }
            });

            // Filter for ACTIVE bookings only
            $query->where(function($q) use ($modelClass) {
                $activeStatuses = ['aktif', 'pending', 'proses', 'dikonfirmasi', 'menunggu', 'bayar'];
                
                if (in_array($modelClass, [BookingFutsal::class, BookingAc::class])) {
                    // Check parent booking status
                    $q->whereHas('booking', function($bq) use ($activeStatuses) {
                        $bq->whereIn('status', $activeStatuses);
                    });
                } elseif ($modelClass === BookingServis::class) {
                    $q->whereIn('status', $activeStatuses);
                } elseif ($modelClass === SewaRuko::class) {
                    $q->whereIn('status_sewa', $activeStatuses);
                } else {
                    $q->whereIn('status', $activeStatuses);
                }
            });

            $results = $results->concat($query->get());
        }

        // unique by access_token to prevent showing same booking multiple times
        return $results->unique('access_token');
    }

    /**
     * Get descriptive unit name for the message.
     */
    private function getUnitName($booking)
    {
        if ($booking instanceof BookingFutsal) return "Lapangan Futsal";
        if ($booking instanceof BookingAc) return "Servis AC";
        if ($booking instanceof BookingServis) {
            $layanan = $booking->layananServis->nama ?? 'Servis Kendaraan';
            return "Servis {$layanan}";
        }
        if ($booking instanceof SewaRuko) return "Sewa Ruko: " . ($booking->ruko->nama_ruko ?? 'Kantin');
        return "Booking Umum";
    }

        public function kirimOtp(Request $request)
    {
        // Delay buatan 0.8 - 1.5 detik agar response time konsisten (Anti-Timing Attack)
        usleep(rand(800000, 1500000));

        $noHp = preg_replace('/[^0-9]/', '', $request->no_hp);

        // 1. Rate Limit per Nomor HP (Maks 3 request per 2 menit)
        $keyHpLimit = 'otp_limit_hp_' . $noHp;
        $countHp = Cache::get($keyHpLimit, 0);
        if ($countHp >= 3) {
            // Tetap return sukses agar tidak membocorkan info (Anti-Enumeration)
            return response()->json(['success' => true], 200);
        }

        // 2. Rate Limit per IP Address (Maks 10 request per menit)
        $keyIpLimit = 'otp_limit_ip_' . $request->ip();
        $countIp = Cache::get($keyIpLimit, 0);
        if ($countIp >= 10) {
            return response()->json(['success' => true], 200);
        }

        // Increment rate limits
        Cache::put($keyHpLimit, $countHp + 1, now()->addMinutes(2));
        Cache::put($keyIpLimit, $countIp + 1, now()->addMinutes(1));

        // Cari booking (Hanya untuk validasi keberadaan nomor)
        $booking = BookingFutsal::where('no_hp', 'like', '%' . substr($noHp, -9))
            ->whereNotIn('status', ['dibatalkan'])
            ->latest()
            ->first();

        if ($booking) {
            // Cek Cooldown (Jarak antar pengiriman minimal 2 menit)
            $keyCooldown = 'otp_cooldown_' . $noHp;
            if (!Cache::has($keyCooldown)) {
                $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                
                // Hash OTP sebelum disimpan (Security Best Practice)
                $otpHash = hash('sha256', $otp . $noHp);
                Cache::put('otp_hash_' . $noHp, $otpHash, now()->addMinutes(5));
                Cache::put('otp_attempt_' . $noHp, 0, now()->addMinutes(5));
                Cache::put($keyCooldown, true, now()->addMinutes(2));

                $this->kirimWhatsapp($noHp, $otp);

                // Log Aktivitas
                $this->logAktivitas($noHp, 'otp_dikirim', $request->ip());
            }
        } else {
            // Nomor tidak ditemukan di sistem, tetap log untuk deteksi enumerasi massal
            $this->logAktivitas($noHp, 'nomor_tidak_ada', $request->ip());
        }

        // SELALU return sukses agar tidak bisa membedakan nomor terdaftar atau tidak
        return response()->json(['success' => true], 200);
    }

    public function verifikasi(Request $request)
    {
        // Delay konsisten 0.5 - 1 detik
        usleep(rand(500000, 1000000));

        $noHp = preg_replace('/[^0-9]/', '', $request->no_hp);
        $otp  = $request->otp;

        // 3. Batas Percobaan (Max 5x salah per nomor)
        $keyAttempt = 'otp_attempt_' . $noHp;
        $attempt    = (int) Cache::get($keyAttempt, 0);

        if ($attempt >= 5) {
            $this->logAktivitas($noHp, 'otp_blokir_percobaan', $request->ip());
            return response()->json([
                'success' => false,
                'message' => 'Terlalu banyak percobaan yang salah. Silakan minta kode OTP baru.'
            ]);
        }

        // Verifikasi dengan Hash
        $otpHashTersimpan = Cache::get('otp_hash_' . $noHp);
        $otpHashInput     = hash('sha256', $otp . $noHp);

        if (!$otpHashTersimpan || $otpHashInput !== $otpHashTersimpan) {
            // Increment percobaan gagal
            $newAttempt = $attempt + 1;
            Cache::put($keyAttempt, $newAttempt, now()->addMinutes(5));
            $this->logAktivitas($noHp, 'otp_salah', $request->ip());
            
            $sisa = 5 - $newAttempt;
            return response()->json([
                'success' => false,
                'message' => "Kode OTP salah. Sisa {$sisa} percobaan."
            ]);
        }

        // OTP Benar - Hapus semua cache OTP (Sekali pakai)
        Cache::forget('otp_hash_' . $noHp);
        Cache::forget('otp_attempt_' . $noHp);
        Cache::forget('otp_cooldown_' . $noHp);

        // Generate Session Token Unik (Token-based access)
        $sessionToken = bin2hex(random_bytes(32));
        $sessionKey   = 'cek_booking_session_' . $sessionToken;

        Cache::put($sessionKey, [
            'no_hp'      => $noHp,
            'ip'         => $request->ip(),
            'created_at' => now()->toDateTimeString(),
        ], now()->addMinutes(15)); // Token hanya berlaku 15 menit

        $this->logAktivitas($noHp, 'verifikasi_sukses', $request->ip());

        return response()->json([
            'success'  => true,
            'redirect' => route('user.cek.booking.riwayat', ['session_token' => $sessionToken])
        ]);
    }

    public function riwayat(Request $request)
    {
        $sessionToken = $request->session_token;
        $sessionKey   = 'cek_booking_session_' . $sessionToken;
        $sessionData  = Cache::get($sessionKey);

        // Validasi Session Token
        if (!$sessionData) {
            return redirect()->route('user.gateway')
                ->with('error', 'Sesi telah berakhir atau tidak valid. Silakan verifikasi ulang.');
        }

        // Opsional: Validasi IP sama untuk mencegah session hijacking
        if ($sessionData['ip'] !== $request->ip()) {
            return redirect()->route('user.gateway');
        }

        $noHp = $sessionData['no_hp'];
        
        // Find all bookings for this phone (Menggunakan method existing)
        $bookings = $this->findAllBookingsByPhone($noHp);
        
        $this->logAktivitas($noHp, 'akses_riwayat', $request->ip());

        return view('user.cek-booking.riwayat', [
            'bookings'     => $bookings,
            'no_hp'        => $noHp,
            'sessionToken' => $sessionToken
        ]);
    }

    private function logAktivitas(string $noHp, string $aksi, string $ip): void
    {
        try {
            CekBookingLog::create([
                'no_hp'      => $noHp,
                'aksi'       => $aksi,
                'ip_address' => $ip,
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Exception $e) {
            Log::warning('CekBookingLog gagal: ' . $e->getMessage());
        }
    }

    private function kirimWhatsapp(string $noHp, string $otp): void
    {
        $apiToken = config('services.fonnte.token');
        $pesan = "🔍 *Cek Booking BLUD Portal*\n\n" .
                 "Kode OTP Anda: *{$otp}*\n\n" .
                 "Berlaku selama *5 menit*.\n" .
                 "Jangan bagikan kode ini kepada siapapun.";

        try {
            Http::withHeaders(['Authorization' => $apiToken])
                ->asForm()
                ->post('https://api.fonnte.com/send', [
                    'target'      => $noHp,
                    'message'     => $pesan,
                    'countryCode' => '62',
                ]);
        } catch (\Exception $e) {
            Log::warning('Gagal kirim OTP cek booking: ' . $e->getMessage());
        }
    }
}
