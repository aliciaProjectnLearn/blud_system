<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SewaRuko;
use App\Models\PembayaranRuko;
use App\Models\HistoryPembayaranRuko;
use App\Models\Ruko;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SewaTokenController extends Controller
{
    public function detail($token)
    {
        $sewa = SewaRuko::where('access_token', $token)->first();

        // Token tidak ditemukan
        if (!$sewa) {
            return view('user.kantin.token.invalid', [
                'pesan' => 'Link tidak valid atau tidak ditemukan.'
            ]);
        }

        // Token sudah expired (waktu)
        if ($sewa->token_expired_at && now()->isAfter($sewa->token_expired_at)) {
            return view('user.kantin.token.invalid', [
                'pesan' => 'Link akses ini sudah kadaluarsa.'
            ]);
        }

        // Lanjut ke verifikasi OTP
        if (!session('sewa_verified_' . $token)) {
            return redirect()->route('user.kantin.otp.form', $token);
        }

        $sewa->load(['ruko', 'ruko.kategori', 'user', 'pembayaran', 'dokumen']);
        return view('user.kantin.token.detail', compact('sewa'));
    }

    public function showOtpForm($token)
    {
        $sewa = SewaRuko::where('access_token', $token)->firstOrFail();
        return view('user.kantin.token.otp', compact('sewa'));
    }

    public function verifyOtp(Request $request, $token)
    {
        $request->validate([
            'otp' => 'required|numeric|digits:6',
        ]);

        $sewa = SewaRuko::where('access_token', $token)->firstOrFail();
        
        $attemptsKey = 'otp_attempts_' . $token;
        $attempts = Cache::get($attemptsKey, 0);

        if ($attempts >= 5) {
            return back()->withErrors(['otp' => 'Terlalu banyak percobaan yang salah. Silakan kirim ulang kode OTP baru.']);
        }

        $cachedOtp = Cache::get('otp_' . $token);

        if ($request->otp == $cachedOtp) {
            // REVISION 11: Store session flag
            session([
                'token_verified' => true,
                'token_verified_at' => now(),
                'booking_token' => $token,
            ]);

            Cache::forget('otp_' . $token);
            Cache::forget($attemptsKey);

            return redirect()->route('user.kantin.sewa.detail', $token)
                ->with('success', 'Verifikasi berhasil. Selamat datang!');
        }

        // Increment attempts
        Cache::put($attemptsKey, $attempts + 1, now()->addMinutes(10));

        return back()->withErrors(['otp' => 'Kode OTP tidak valid atau sudah kadaluwarsa.']);
    }

    public function resendOtp($token)
    {
        $sewa = SewaRuko::where('access_token', $token)->firstOrFail();
        
        // Clear attempts on resend
        Cache::forget('otp_attempts_' . $token);
        
        $this->sendOtp($sewa);
        return back()->with('success', 'Kode OTP baru telah dikirim.');
    }

    private function sendOtp($sewa)
    {
        $otp = rand(100000, 999999);
        // Set OTP expiry to 3 minutes as requested
        Cache::put('otp_' . $sewa->access_token, $otp, now()->addMinutes(3));

        $apiToken = config('services.fonnte.token');
        if (!$apiToken) {
            Log::warning('Fonnte API Token tidak ditemukan di config/services.php');
            return;
        }

        $pesan = "Kode OTP Anda adalah: *{$otp}*. Berlaku selama 3 menit. Jangan berikan kode ini kepada siapapun.";

        $target = preg_replace('/[^0-9]/', '', $sewa->no_hp_snapshot);
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
                Log::warning('Fonnte OTP Send Error: ' . ($resBody['reason'] ?? $response->body()));
            } else {
                Log::info('OTP Berhasil Dikirim ke Fonnte: ' . $target);
            }
        } catch (\Exception $e) {
            Log::error('Gagal Kirim OTP WA via Fonnte: ' . $e->getMessage());
        }
    }

    public function pembayaran(Request $request, $token)
    {
        $sewa = SewaRuko::with('pembayaran')->where('access_token', $token)->firstOrFail();
        
        $pembayaran_id = $request->query('pembayaran_id');
        if ($pembayaran_id) {
            $pembayaran = $sewa->pembayaran->where('id', $pembayaran_id)->first();
        } else {
            $pembayaran = $sewa->pembayaran->whereIn('status_pembayaran', ['pending', 'ditolak'])->sortBy('termin_ke')->first();
        }

        if (!$pembayaran) {
            return redirect()->route('user.kantin.sewa.detail', $token)->with('error', 'Tidak ada tagihan yang harus dibayar saat ini.');
        }

        $total = $sewa->pembayaran->sum('jumlah_tagihan');
        return view('user.kantin.token.upload-bukti', [
            'sewa' => $sewa,
            'pembayaran' => $pembayaran,
            'total' => $total
        ]);
    }

    public function uploadBukti(Request $request, $token)
    {
        $request->validate([
            'pembayaran_id' => 'required|exists:pembayaran_ruko,id',
            'bukti' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $sewa = SewaRuko::where('access_token', $token)->firstOrFail();
        $pembayaran = PembayaranRuko::where('sewa_ruko_id', $sewa->id)->findOrFail($request->pembayaran_id);

        if ($request->hasFile('bukti')) {
            $path = $request->file('bukti')->store('bukti-pembayaran', 'public');
            
            $metode = $request->metode_pembayaran;
            $tipe_id = null;
            if ($metode === 'transfer') $tipe_id = 1;
            elseif ($metode === 'tunai') $tipe_id = 2;
            elseif ($metode === 'qris') $tipe_id = 3;

            $pembayaran->update([
                'bukti_pembayaran' => $path,
                'status_pembayaran' => 'menunggu_verifikasi',
                'tanggal_bayar' => now(),
                'tipe_pembayaran_id' => $tipe_id,
            ]);

            HistoryPembayaranRuko::create([
                'pembayaran_ruko_id' => $pembayaran->id,
                'aksi' => 'upload_bukti',
                'keterangan' => 'User mengunggah bukti pembayaran untuk termin ke-' . $pembayaran->termin_ke,
                'dilakukan_oleh' => $sewa->nama_penyewa,
            ]);
        }

        return redirect()->route('user.kantin.sewa.detail', $token)->with('success', 'Bukti pembayaran berhasil diunggah.');
    }

    public function dokumen($token)
    {
        $sewa = SewaRuko::with('dokumen')->where('access_token', $token)->firstOrFail();
        return view('user.kantin.token.dokumen', compact('sewa'));
    }

    public function batalkan(Request $request, $token)
    {
        $sewa = SewaRuko::where('access_token', $token)->firstOrFail();

        if ($sewa->status_sewa !== 'pending') {
            return back()->with('error', 'Hanya sewa dengan status pending yang dapat dibatalkan.');
        }

        DB::beginTransaction();
        try {
            $sewa->update(['status_sewa' => 'dibatalkan']);
            
            $ruko = Ruko::find($sewa->ruko_id);
            if ($ruko) {
                $ruko->update(['status' => 'tersedia']);
            }

            HistoryPembayaranRuko::create([
                'aksi' => 'pembatalan',
                'keterangan' => 'Sewa dibatalkan oleh pengguna via token.',
                'dilakukan_oleh' => $sewa->nama_penyewa,
            ]);

            DB::commit();
            return redirect()->route('user.kantin.sewa.detail', $token)->with('success', 'Sewa berhasil dibatalkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membatalkan: ' . $e->getMessage());
        }
    }
}
