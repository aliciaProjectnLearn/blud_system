<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Ruko;
use App\Models\SewaRuko;
use App\Models\PembayaranRuko;
use App\Models\User;
use App\Models\Penyewa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class KantinController extends Controller
{
    public function index()
    {
        // Tetap arahkan ke katalog sebagai halaman utama kantin
        return $this->katalog();
    }

    public function katalog()
    {
        // View asli menggunakan rukoList dan rukoDataJs untuk SVG
        $rukoList = Ruko::with(['kategori', 'dokumentasiUnit'])->get();
        $rukoDataJs = $rukoList->map(function($r) {
            return [
                'id' => $r->id,
                'kode_unit' => $r->kode_unit,
                'status_unit' => $r->status === 'tersedia' ? 'kosong' : 'terisi',
                'harga' => $r->harga,
                'kategori_id' => $r->kategori_id,
                'kategori' => ['nama' => $r->kategori->nama ?? 'Umum']
            ];
        });

        return view('user.kantin.katalog', compact('rukoList', 'rukoDataJs'));
    }

    public function formBooking($ruko_id = null)
    {
        // View asli menggunakan 'units' untuk dropdown dan 'penyewa' untuk check auth
        $units = Ruko::where('status', 'tersedia')->get();
        $ruko = $ruko_id ? Ruko::find($ruko_id) : null;
        $penyewa = Auth::user() ? Auth::user()->penyewa : null;

        return view('user.kantin.booking-form', compact('units', 'ruko', 'penyewa'));
    }

    public function simpanBooking(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'no_hp' => 'required|numeric|digits_between:10,13',
            'nik' => 'required|numeric|digits:16',
            'ruko_id' => 'required|exists:ruko,id',
            'tanggal_mulai_sewa' => 'required|date|after_or_equal:today',
            'tipe_pembayaran' => 'required|in:1_termin,2_termin',
        ]);

        $ruko = Ruko::findOrFail($request->ruko_id);
        if ($ruko->status !== 'tersedia') {
            return back()->with('error', 'Unit sudah tidak tersedia.');
        }

        DB::beginTransaction();
        try {
            // 1. Cek/Buat User (Revision: No Login Required)
            $user = User::where('no_hp', $request->no_hp)->first();
            if (!$user) {
                $user = User::create([
                    'name'         => $request->nama,
                    'username'     => $request->nama,
                    'nama_lengkap' => $request->nama,
                    'email'        => $request->no_hp . '@example.com',
                    'no_hp'        => $request->no_hp,
                    'nik'          => $request->nik,
                    'password'     => bcrypt(Str::random(16)),
                ]);
            }

            // 2. Generate Token
            $token = bin2hex(random_bytes(32));

            // 3. Simpan SewaRuko
            $tgl_mulai = Carbon::parse($request->tanggal_mulai_sewa);
            $tgl_selesai = $tgl_mulai->copy()->addYear();

            $sewa = SewaRuko::create([
                'user_id' => $user->id,
                'ruko_id' => $ruko->id,
                'access_token' => $token,
                'nama_penyewa' => $request->nama,
                'no_hp_snapshot' => $request->no_hp,
                'nik_penyewa' => $request->nik,
                'status_sewa' => 'pending',
                'tanggal_mulai_sewa' => $tgl_mulai,
                'tanggal_selesai_sewa' => $tgl_selesai,
                'tipe_pembayaran' => $request->tipe_pembayaran,
            ]);

            // 4. Buat Pembayaran
            $total_harga = $ruko->harga;
            if ($request->tipe_pembayaran === '1_termin') {
                PembayaranRuko::create([
                    'sewa_ruko_id' => $sewa->id,
                    'termin_ke' => 1,
                    'jumlah_tagihan' => $total_harga,
                    'status_pembayaran' => 'pending',
                    'tgl_jatuh_tempo' => $tgl_mulai,
                ]);
            } else {
                $setengah = $total_harga / 2;
                PembayaranRuko::create([
                    'sewa_ruko_id' => $sewa->id,
                    'termin_ke' => 1,
                    'jumlah_tagihan' => $setengah,
                    'status_pembayaran' => 'pending',
                    'tgl_jatuh_tempo' => $tgl_mulai,
                ]);
                PembayaranRuko::create([
                    'sewa_ruko_id' => $sewa->id,
                    'termin_ke' => 2,
                    'jumlah_tagihan' => $total_harga - $setengah,
                    'status_pembayaran' => 'pending',
                    'tgl_jatuh_tempo' => $tgl_mulai->copy()->addMonths(6),
                ]);
            }

            // 5. Update Ruko
            $ruko->update(['status' => 'disewa']);

            // 6. Kirim Notifikasi WhatsApp
            $sewa->load('ruko');
            $this->sendWhatsAppNotification($sewa);

            DB::commit();

            return redirect()->route('user.kantin.katalog')
                ->with('booking_sukses', true)
                ->with('booking_token', $token)
                ->with('booking_pesan', 
                    'Pengajuan sewa telah berhasil dikirim. '.
                    'Periksa status sewa Anda dengan klik link '.
                    'yang kami kirimkan melalui WhatsApp.'
                );

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function unitDetail($id)
    {
        $unit = Ruko::with(['kategori', 'dokumentasiUnit'])->findOrFail($id);
        
        // Sesuaikan data agar kompatibel dengan JavaScript di booking-form.blade.php
        $data = $unit->toArray();
        $data['dokumentasi'] = $unit->dokumentasiUnit->map(function($d) {
            return [
                'tipe' => 'gambar', // Defaultkan sebagai gambar agar JS filter jalan
                'url' => asset('storage/' . ($d->file ?? $d->path_file))
            ];
        });

        return response()->json($data);
    }

    private function sendWhatsAppNotification($sewa)
    {
        $apiToken = config('services.fonnte.token');
        if (!$apiToken) {
            Log::warning('Fonnte API Token tidak ditemukan di config/services.php');
            return;
        }

        // Ambil nama ruko dari tabel ruko jika dibutuhkan, default ke 'Kantin'
        $nama_ruko = $sewa->ruko->nama_ruko ?? 'Kantin / Ruko';
        
        $link = route('user.kantin.sewa.detail', ['token' => $sewa->access_token]);
        $pesan = "Halo *{$sewa->nama_penyewa}* 👋\n\n";
        $pesan .= "Booking unit *{$nama_ruko}* ({$sewa->ruko->kode_unit}) berhasil dilakukan.\n\n";
        $pesan .= "Silakan akses detail penyewaan dan unggah bukti pembayaran melalui link berikut:\n";
        $pesan .= "🔗 {$link}\n\n";
        $pesan .= "Mohon simpan link ini sebagai akses ke sistem Sewa Kantin BLUD SMK.\n";
        $pesan .= "— Admin Kantin BLUD";

        // Bersihkan nomor HP: hapus karakter non-digit
        $target = preg_replace('/[^0-9]/', '', $sewa->no_hp_snapshot);
        
        // Pastikan format internasional (08xxx -> 628xxx)
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
                // countryCode tidak perlu jika target sudah 62, tapi aman untuk tetap ada
                'countryCode' => '62', 
            ]);

            $resBody = $response->json();
            
            if ($response->failed() || ($resBody['status'] ?? false) == false) {
                Log::error('Fonnte API Error: ' . ($resBody['reason'] ?? $response->body()));
            } else {
                Log::info('WhatsApp Berhasil Dikirim ke Fonnte: ' . $target . ' | Response: ' . json_encode($resBody));
            }
        } catch (\Exception $e) {
            Log::error('Gagal Kirim WA Fonnte: ' . $e->getMessage());
        }
    }
}
