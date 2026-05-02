<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingFutsal;
use App\Models\BookingAc;
use App\Models\BookingServis;
use App\Models\SewaRuko;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TokenAccessController extends Controller
{
    /**
     * Tampilkan detail booking berdasarkan token.
     */
    public function show($token)
    {
        $booking = $this->findBookingByToken($token);

        if (!$booking) {
            return abort(404, 'Token tidak valid atau booking tidak ditemukan.');
        }

        // REVISION 11 & 12: Session Check & Auto OTP Send
        $sessionToken = session('booking_token');
        $isVerified = session('token_verified') === true && 
                     $sessionToken === $token &&
                     session('token_verified_at') && 
                     Carbon::parse(session('token_verified_at'))->addMinutes(60)->isFuture();

        if (!$isVerified) {
            $this->sendOtp($booking, $token);
            return redirect()->route('token.otp', ['token' => $token])
                ->with('info', 'Sesi Anda telah berakhir. Kode OTP baru telah dikirim ke WhatsApp Anda.');
        }

        $type = $this->getBookingType($booking);
        $user = $booking->user ?? $booking->penyewaUser ?? null; // Adjust based on model relations

        return view('user.token.detail', compact('booking', 'type', 'token', 'user'));
    }

    public function showOtpForm($token)
    {
        $booking = $this->findBookingByToken($token);
        if (!$booking) return abort(404);
        
        return view('user.token.otp', compact('booking', 'token'));
    }

    public function verifyOtp(Request $request, $token)
    {
        $request->validate(['otp' => 'required|numeric|digits:6']);
        
        $attemptsKey = 'otp_attempts_' . $token;
        $attempts = Cache::get($attemptsKey, 0);

        if ($attempts >= 5) {
            return back()->withErrors(['otp' => 'Terlalu banyak percobaan yang salah. Silakan kirim ulang kode OTP baru.']);
        }

        $cachedOtp = Cache::get('otp_' . $token);

        if ($request->otp == $cachedOtp) {
            session([
                'token_verified' => true,
                'token_verified_at' => now(),
                'booking_token' => $token,
            ]);
            Cache::forget('otp_' . $token);
            Cache::forget($attemptsKey);
            return redirect()->route('token.show', $token);
        }

        // Increment attempts
        Cache::put($attemptsKey, $attempts + 1, now()->addMinutes(10));

        return back()->withErrors(['otp' => 'Kode OTP tidak valid.']);
    }

    public function resendOtp($token)
    {
        $booking = $this->findBookingByToken($token);
        if (!$booking) return abort(404);
        
        // Clear attempts on resend
        Cache::forget('otp_attempts_' . $token);
        
        $this->sendOtp($booking, $token);
        return back()->with('success', 'Kode OTP baru telah dikirim.');
    }

    private function sendOtp($booking, $token)
    {
        $otp = rand(100000, 999999);
        // Set OTP expiry to 3 minutes as requested
        Cache::put('otp_' . $token, $otp, now()->addMinutes(3));

        $noHp = $booking->no_hp ?? ($booking->user->no_hp ?? ($booking->no_hp_snapshot ?? null));
        if (!$noHp) {
            Log::warning('No. HP tidak ditemukan untuk token: ' . $token);
            return;
        }

        $apiToken = config('services.fonnte.token');
        if (!$apiToken) {
            Log::warning('Fonnte API Token tidak ditemukan di config/services.php');
            return;
        }

        $pesan = "Kode OTP Anda adalah: *{$otp}*. Berlaku selama 3 menit. Jangan berikan kode ini kepada siapapun.";

        $target = preg_replace('/[^0-9]/', '', $noHp);
        if (str_starts_with($target, '0')) {
            $target = '62' . substr($target, 1);
        } elseif (!str_starts_with($target, '62')) {
            $target = '62' . $target;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $apiToken,
            ])->post('https://api.fonnte.com/send', [
                'target'      => $target,
                'message'     => $pesan,
                'countryCode' => '62',
            ]);

            $resBody = $response->json();
            
            if ($response->failed() || ($resBody['status'] ?? false) == false) {
                Log::warning('Fonnte Generic OTP Send Error: ' . ($resBody['reason'] ?? $response->body()));
            } else {
                Log::info('OTP Generic Berhasil Dikirim ke Fonnte: ' . $target);
            }
        } catch (\Exception $e) {
            Log::error('Gagal Kirim OTP Generic WA via Fonnte: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan riwayat semua booking berdasarkan nomor HP dari token yang diberikan.
     */
    // REVISION 3: Riwayat removed
    /*
    public function riwayat($token)
    {
        ...
    }
    */

    /**
     * Tampilkan halaman konfirmasi pembatalan.
     */
    public function batalkan($token)
    {
        $booking = $this->findBookingByToken($token);

        if (!$booking) {
            return abort(404, 'Token tidak valid.');
        }

        // Validasi status & waktu pembatalan
        $canCancel = $this->checkCanCancel($booking);

        if (!$canCancel['allowed']) {
            return redirect()->route('user.token.show', $token)->with('error', $canCancel['message']);
        }

        return view('user.token.batalkan', compact('booking', 'token'));
    }

    /**
     * Proses pembatalan booking.
     */
    public function prosesBatalkan(Request $request, $token)
    {
        $booking = $this->findBookingByToken($token);

        if (!$booking) {
            return abort(404, 'Token tidak valid.');
        }

        $canCancel = $this->checkCanCancel($booking);
        if (!$canCancel['allowed']) {
            return redirect()->route('user.token.show', $token)->with('error', $canCancel['message']);
        }

        // Update status
        if (isset($booking->status)) {
            if ($booking instanceof \App\Models\BookingServis) {
                $booking->status = 'batal';
            } else {
                $booking->status = 'dibatalkan';
            }
            $booking->save();
        }
        
        // If it has a parent booking table record
        if (isset($booking->booking_id) && $booking->booking) {
            $booking->booking->status = 'dibatalkan';
            $booking->booking->save();
        }

        return redirect()->route('user.token.show', $token)->with('success', 'Booking berhasil dibatalkan.');
    }

    /**
     * Helper to find booking across tables by token.
     */
    private function findBookingByToken($token)
    {
        return BookingFutsal::where('access_token', $token)->first()
            ?? BookingAc::where('access_token', $token)->first()
            ?? BookingServis::where('access_token', $token)->first()
            ?? SewaRuko::where('access_token', $token)->first()
            ?? Booking::where('access_token', $token)->first();
    }

    private function getBookingType($booking)
    {
        if ($booking instanceof BookingFutsal) return 'futsal';
        if ($booking instanceof BookingAc) return 'ac';
        if ($booking instanceof BookingServis) return 'servis';
        if ($booking instanceof SewaRuko) return 'kantin';
        return 'umum';
    }

    private function checkCanCancel($booking)
    {
        $status = strtolower($booking->status ?? ($booking->booking->status ?? ''));
        
        if (in_array($status, ['selesai', 'dibatalkan', 'proses', 'diproses'])) {
            return ['allowed' => false, 'message' => 'Booking dengan status ' . $status . ' tidak dapat dibatalkan.'];
        }

        // Add time-based validation if needed (e.g., max 24h before)
        // For now, let's allow if status is 'menunggu' or 'dikonfirmasi'
        
        return ['allowed' => true, 'message' => ''];
    }
}
