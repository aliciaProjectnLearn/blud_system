<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingAc;
use App\Models\BookingFutsal;
use App\Models\BookingServis;
use App\Models\SewaRuko;
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
}
