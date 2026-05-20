<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BookingAc;
use App\Models\OtpToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class AcTokenController extends Controller
{
    public function show(Request $request, $token)
    {
        $booking = BookingAc::with(['layanan', 'booking', 'pembayaran', 'teknisi', 'detailServis'])
            ->where('access_token', $token)->firstOrFail();

        // Jika booking sudah selesai, ditolak, atau dibatalkan, link tidak aktif lagi
        $status = $booking->booking->status ?? 'N/A';
        if (in_array($status, ['selesai', 'ditolak', 'dibatalkan'])) {
            return view('user.token.expired', ['status' => $status]);
        }

        // [6] Check session (60 minutes)
        if ($request->session()->has('otp_verified_' . $token)) {
            $verifiedAt = $request->session()->get('otp_verified_' . $token);
            if (Carbon::parse($verifiedAt)->addMinutes(60)->isFuture()) {
                return view('user.ac.token_detail', compact('booking', 'token'));
            } else {
                $request->session()->forget('otp_verified_' . $token);
            }
        }

        // Redirect to OTP form
        return redirect()->route('user.ac.token.otp', $token);
    }

    public function otpForm(Request $request, $token)
    {
        $booking = BookingAc::where('access_token', $token)->firstOrFail();

        // Check session again
        if ($request->session()->has('otp_verified_' . $token)) {
            $verifiedAt = $request->session()->get('otp_verified_' . $token);
            if (Carbon::parse($verifiedAt)->addMinutes(60)->isFuture()) {
                return redirect()->route('user.ac.token.show', $token);
            }
        }

        // [3] Check if we need to send OTP (first access or expired)
        $otp = OtpToken::where('access_token', $token)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$otp) {
            $this->sendOtpNotification($booking);
        }

        $otp = OtpToken::where('access_token', $token)
            ->latest()
            ->first();

        return view('user.ac.otp_verify', compact('booking', 'token', 'otp'));
    }

    public function verifyOtp(Request $request, $token)
    {
        $request->validate(['otp' => 'required|string|size:6']);
        $booking = BookingAc::where('access_token', $token)->firstOrFail();

        $otp = OtpToken::where('access_token', $token)
            ->where('is_used', false)
            ->latest()
            ->first();

        if (!$otp || $otp->expires_at->isPast()) {
            return back()->with('error', 'OTP kadaluarsa. Silakan kirim ulang.');
        }

        // [4] Check block
        if ($otp->blocked_until && $otp->blocked_until->isFuture()) {
            $diff = $otp->blocked_until->diffInSeconds(now());
            return back()->with('error', "Akses diblokir sementara. Silakan coba lagi dalam $diff detik.");
        }

        if ($otp->otp_code !== $request->otp) {
            $otp->increment('attempt_count');
            if ($otp->attempt_count >= 3) {
                $otp->update(['blocked_until' => now()->addMinutes(3)]);
                return back()->with('error', 'Terlalu banyak percobaan salah. Akses diblokir selama 3 menit.');
            }
            return back()->with('error', 'OTP salah. Sisa percobaan: ' . (3 - $otp->attempt_count));
        }

        // Success
        $otp->update(['is_used' => true, 'blocked_until' => null, 'attempt_count' => 0]);
        $request->session()->put('otp_verified_' . $token, now());

        return redirect()->route('user.ac.token.show', $token);
    }

    public function resendOtp(Request $request, $token)
    {
        $booking = BookingAc::where('access_token', $token)->firstOrFail();
        
        $lastOtp = OtpToken::where('access_token', $token)->latest()->first();
        
        if ($lastOtp) {
            // Cooldown 60s
            if ($lastOtp->sent_at && $lastOtp->sent_at->addSeconds(60)->isFuture()) {
                $wait = $lastOtp->sent_at->addSeconds(60)->diffInSeconds(now());
                return back()->with('error', "Tunggu $wait detik sebelum mengirim ulang.");
            }
            
            // Blocked check
            if ($lastOtp->blocked_until && $lastOtp->blocked_until->isFuture()) {
                return back()->with('error', 'Akses masih diblokir.');
            }
        }

        $this->sendOtpNotification($booking);

        return back()->with('success', 'OTP telah dikirim ulang ke nomor WhatsApp Anda.');
    }

    private function sendOtpNotification($booking)
    {
        $token = $booking->access_token;
        $otp = OtpToken::where('access_token', $token)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->first();

        if ($otp) {
            $code = $otp->otp_code;
            $otp->update(['sent_at' => now()]);
        } else {
            $code = sprintf("%06d", mt_rand(0, 999999));
            OtpToken::create([
                'access_token' => $token,
                'otp_code' => $code,
                'expires_at' => now()->addMinutes(3),
                'sent_at' => now(),
            ]);
        }

        $apiToken = config('services.fonnte.token');
        if (!$apiToken) {
            Log::warning("Fonnte Token not found in .env. OTP: $code");
            return;
        }

        $pesan = "*KODE OTP AKSES BOOKING* 🔒\n\n";
        $pesan .= "Kode OTP Anda adalah: *{$code}*\n\n";
        $pesan .= "Kode ini berlaku selama 3 menit. Jangan berikan kode ini kepada siapa pun.\n\n";
        $pesan .= "BLUD System";

        try {
            Http::withHeaders([
                'Authorization' => $apiToken,
            ])->post('https://api.fonnte.com/send', [
                'target' => $booking->no_hp,
                'message' => $pesan,
                'countryCode' => '62',
            ]);
        } catch (\Exception $e) {
            Log::error("Fonnte OTP Error: " . $e->getMessage());
        }
    }
}
