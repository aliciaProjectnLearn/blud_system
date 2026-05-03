<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BookingFutsal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TokenAccessController extends Controller
{
    public function show($token)
    {
        $bookingFutsal = BookingFutsal::with(['booking', 'lapangan'])
            ->where('access_token', $token)->firstOrFail();

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

    public function batalkan($token)
    {
        $bookingFutsal = BookingFutsal::with(['booking', 'lapangan'])
            ->where('access_token', $token)->firstOrFail();

        $canCancel = $bookingFutsal->status === 'menunggu' && 
                     Carbon::parse($bookingFutsal->start_datetime)->gt(now()->addHours(2));

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

        $canCancel = $bookingFutsal->status === 'menunggu' && 
                     Carbon::parse($bookingFutsal->start_datetime)->gt(now()->addHours(2));

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
}
