<?php

namespace App\Http\Controllers;

use App\Models\Ruko;
use App\Models\SewaRuko;
use App\Models\PembayaranRuko;
use App\Models\User;
use App\Models\Penyewa;
use App\Models\Booking;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class KantinBookingController extends Controller
{
    public function index()
    {
        $rukoList = Ruko::whereHas('kategori', function($q) {
            $q->where('tipe', 'kantin');
        })->get();

        return view('user.kantin.booking', compact('rukoList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_penyewa'         => 'required|string|max:255',
            'nik'                  => 'required|digits:16',
            'no_hp'                => 'required|string|max:20',
            'ruko_id'              => 'required|exists:ruko,id',
            'tipe_pembayaran'      => 'required|in:1_termin,2_termin',
            'tanggal_mulai_sewa'   => 'required|date|after_or_equal:today',
            'tanggal_selesai_sewa' => 'required|date|after:tanggal_mulai_sewa',
        ]);

        DB::beginTransaction();
        try {
            // 1. Cari atau buat user berdasarkan no_hp
            $user = User::where('no_hp', $request->no_hp)->first();
            if (!$user) {
                $user = User::create([
                    'name'         => $request->nama_penyewa,
                    'username'     => 'user_' . Str::random(8),
                    'nama_lengkap' => $request->nama_penyewa,
                    'no_hp'        => $request->no_hp,
                    'nik'          => $request->nik,
                    'password'     => bcrypt(Str::random(16)),
                ]);
                
                // Assign Pelanggan role
                $pelangganRole = Role::where('nama', 'Pelanggan')->first();
                if ($pelangganRole) {
                    $user->roles()->attach($pelangganRole->id);
                }
            }

            // 2. Create Penyewa record if not exists
            $penyewa = Penyewa::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'nama_usaha' => $request->nama_penyewa,
                    'alamat'     => '-',
                    'nik'        => $request->nik,
                ]
            );

            // 3. Create Booking parent
            $booking = Booking::create([
                'user_id' => $user->id,
                'status'  => 'menunggu',
            ]);

            // 4. Generate access token
            $token = SewaRuko::generateToken();

            // 5. Simpan SewaRuko
            $ruko = Ruko::findOrFail($request->ruko_id);
            $sewa = SewaRuko::create([
                'access_token'         => $token,
                'penyewa_id'           => $penyewa->id,
                'ruko_id'              => $ruko->id,
                'nama_penyewa'         => $request->nama_penyewa,
                'no_hp_snapshot'       => $request->no_hp,
                'tanggal_mulai_sewa'   => $request->tanggal_mulai_sewa,
                'tanggal_selesai_sewa' => $request->tanggal_selesai_sewa,
                'status_sewa'          => 'aktif',
                'harga_sewa_tahunan'   => $ruko->harga,
                'booking_id'           => $booking->id,
            ]);

            // 6. Buat PembayaranRuko (Termin 1)
            $jumlahTermin1 = ($request->tipe_pembayaran === '2_termin') ? $ruko->harga / 2 : $ruko->harga;
            
            PembayaranRuko::create([
                'sewa_ruko_id'      => $sewa->id,
                'booking_id'        => $booking->id,
                'tipe_pembayaran'   => $request->tipe_pembayaran,
                'termin_ke'         => 1,
                'jumlah_tagihan'    => $jumlahTermin1,
                'status_pembayaran' => 'pending',
                'status'            => 'menunggu',
                'tgl_jatuh_tempo'   => $request->tanggal_mulai_sewa,
            ]);

            // Jika 2 termin, buat record termin 2 sekaligus
            if ($request->tipe_pembayaran === '2_termin') {
                PembayaranRuko::create([
                    'sewa_ruko_id'      => $sewa->id,
                    'booking_id'        => $booking->id,
                    'tipe_pembayaran'   => $request->tipe_pembayaran,
                    'termin_ke'         => 2,
                    'jumlah_tagihan'    => $ruko->harga - $jumlahTermin1,
                    'status_pembayaran' => 'pending',
                    'status'            => 'menunggu',
                    'tgl_jatuh_tempo'   => Carbon::parse($request->tanggal_mulai_sewa)->addMonths(6),
                ]);
            }

            // 7. Update status unit
            $ruko->update(['status_unit' => 'terisi']);

            // 8. Kirim WhatsApp via Fonnte
            $this->sendWhatsAppNotification($sewa);

            DB::commit();

            return redirect()->route('user.kantin.booking.success', ['token' => $token])
                ->with('success', 'Booking berhasil! Link akses telah dikirim ke WhatsApp Anda.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal Booking Kantin: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage())->withInput();
        }
    }

    private function sendWhatsAppNotification($sewa)
    {
        $apiToken = config('services.fonnte.token');
        if (!$apiToken) return;

        $link = route('user.kantin.sewa.token', ['token' => $sewa->access_token]);
        $pesan = "Halo *{$sewa->nama_penyewa}* 👋\n\n";
        $pesan .= "Booking unit *{$sewa->ruko->nama_ruko}* ({$sewa->ruko->kode_unit}) berhasil dilakukan.\n\n";
        $pesan .= "Silakan akses detail penyewaan dan unggah bukti pembayaran melalui link berikut:\n";
        $pesan .= "🔗 {$link}\n\n";
        $pesan .= "Mohon simpan link ini sebagai akses ke sistem Sewa Kantin BLUD SMK.\n";
        $pesan .= "— Admin Kantin BLUD";

        try {
            Http::withHeaders([
                'Authorization' => $apiToken,
            ])->post('https://api.fonnte.com/send', [
                'target'      => $sewa->no_hp_snapshot,
                'message'     => $pesan,
                'countryCode' => '62',
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal Kirim WA Fonnte: ' . $e->getMessage());
        }
    }

    public function success($token)
    {
        $sewa = SewaRuko::where('access_token', $token)->firstOrFail();
        return view('user.kantin.success', compact('sewa'));
    }
}
