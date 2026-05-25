<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SewaRuko;
use App\Models\OtpVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OtpController extends Controller
{
    public function form($token)
    {
        $sewa = SewaRuko::where('access_token', $token)->firstOrFail();
 
        if (session('sewa_verified_' . $token)) {
            return redirect()->route('user.kantin.sewa.detail', $token);
        }

        $this->kirimOtp($sewa);

        $hp = $sewa->no_hp_snapshot;
        $hpSensor = substr($hp, 0, 4) . '****' . substr($hp, -4);

        return view('user.kantin.token.otp', compact('token', 'hpSensor'));
    }

    // Kirim ulang OTP
    public function kirimUlang($token)
    {
        $sewa = SewaRuko::where('access_token', $token)->firstOrFail();
        $this->kirimOtp($sewa);

        return back()->with('info', 'OTP baru telah dikirim ke WhatsApp Anda.');
    }

    // Verifikasi OTP
    public function verifikasi(Request $request, $token)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $sewa = SewaRuko::where('access_token', $token)->firstOrFail();

        $otp = OtpVerification::where('token_sewa', $token)
            ->where('kode_otp', $request->otp)
            ->where('sudah_digunakan', false)
            ->where('expired_at', '>', now())
            ->latest()
            ->first();

        if (!$otp) {
            return back()->with('error', 
                'Kode OTP salah atau sudah kadaluarsa. Silakan coba lagi.'
            );
        }

        // Tandai OTP sudah digunakan
        $otp->update(['sudah_digunakan' => true]);

        // Buat session sementara (berlaku 2 jam)
        session([
            'sewa_verified_' . $token => true,
            'sewa_verified_at_' . $token => now()->toDateTimeString(),
        ]);

        return redirect()->route('user.kantin.sewa.detail', $token);
    }

    // Helper: generate & kirim OTP
    private function kirimOtp(SewaRuko $sewa)
    {
        // Hapus OTP lama yang belum digunakan untuk token ini agar tidak menumpuk
        OtpVerification::where('token_sewa', $sewa->access_token)
            ->where('sudah_digunakan', false)
            ->delete();

        // Generate OTP 6 digit
        $kodeOtp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Simpan ke database
        OtpVerification::create([
            'token_sewa'  => $sewa->access_token,
            'no_hp'       => $sewa->no_hp_snapshot,
            'kode_otp'    => $kodeOtp,
            'expired_at'  => now()->addMinutes(5),
        ]);

        // Kirim via WhatsApp
        $pesan = "🔐 *Kode Verifikasi BLUD Portal*\n\n" .
                 "Kode OTP Anda: *" . $kodeOtp . "*\n\n" .
                 "Berlaku selama *5 menit*.\n" .
                 "Jangan bagikan kode ini kepada siapapun.\n\n" .
                 "_BLUD SMKN 1 Cirebon_";

        $noHp = $sewa->no_hp_snapshot;
        // Format nomor: 08xx → 628xx
        if (str_starts_with($noHp, '0')) {
            $noHp = '62' . substr($noHp, 1);
        } elseif (!str_starts_with($noHp, '62')) {
             $noHp = '62' . $noHp;
        }

        $apiToken = config('services.fonnte.token');
        
        if ($apiToken) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => $apiToken,
                ])->post('https://api.fonnte.com/send', [
                    'target'      => $noHp,
                    'message'     => $pesan,
                    'countryCode' => '62',
                ]);

                if ($response->failed()) {
                    Log::error('Fonnte OTP Error: ' . $response->body());
                }
            } catch (\Exception $e) {
                Log::error('Gagal Kirim OTP WA: ' . $e->getMessage());
            }
        }

        // Log untuk development
        Log::info('OTP WhatsApp Generated', [
            'no_hp' => $noHp,
            'otp'   => $kodeOtp,
        ]);
    }
}
