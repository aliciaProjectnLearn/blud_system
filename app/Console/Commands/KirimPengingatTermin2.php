<?php

namespace App\Console\Commands;

use App\Models\PembayaranRuko;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class KirimPengingatTermin2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kantin:kirim-pengingat-termin2';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mengirim pengingat WhatsApp H-30 untuk pembayaran sewa unit kantin termin 2';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $targetDate = Carbon::today()->addDays(30);
        $this->info("Mencari tagihan termin 2 yang jatuh tempo pada: " . $targetDate->format('Y-m-d'));

        // Cari pembayaran termin 2, status menunggu, jatuh tempo H+30
        $tagihans = PembayaranRuko::with(['sewaRuko.penyewa.user', 'sewaRuko.ruko'])
            ->where('termin', 2)
            ->where('status', 'menunggu')
            ->whereDate('tgl_jatuh_tempo', $targetDate)
            ->get();

        if ($tagihans->isEmpty()) {
            $this->info("Tidak ada tagihan yang jatuh tempo pada tanggal tersebut.");
            return;
        }

        $apiToken = env('FONNTE_TOKEN', 'YOUR_API_TOKEN_HERE');
        if ($apiToken === 'YOUR_API_TOKEN_HERE') {
            $this->error("Token Fonnte belum diatur di .env");
            return;
        }

        foreach ($tagihans as $p) {
            $sewa = $p->sewaRuko;
            $user = $sewa->penyewa->user ?? null;
            $ruko = $sewa->ruko;

            if (!$user || !$user->no_hp) {
                $this->warn("User atau nomor HP tidak ditemukan untuk tagihan ID: {$p->id}");
                continue;
            }

            $namaPenyewa = $user->nama_lengkap ?? $user->name;
            $kodeUnit = $ruko->kode_unit ?? '-';
            $tglJatuhTempo = Carbon::parse($p->tgl_jatuh_tempo)->translatedFormat('d F Y');
            $nominal = number_format($p->jumlah_tagihan, 0, ',', '.');

            $pesan = "Halo *{$namaPenyewa}* 👋\n\n";
            $pesan .= "Kami mengingatkan bahwa pembayaran sewa Termin 2 untuk unit *{$kodeUnit}* akan jatuh tempo pada *{$tglJatuhTempo}*.\n";
            $pesan .= "💰 *Jumlah tagihan: Rp {$nominal}*\n\n";
            $pesan .= "Mohon segera lakukan pembayaran sebelum tanggal tersebut untuk menghindari keterlambatan. Terima kasih 🙏\n";
            $pesan .= "— Admin Kantin BLUD SMK";

            try {
                $response = Http::withHeaders([
                    'Authorization' => $apiToken,
                ])->post('https://api.fonnte.com/send', [
                    'target'      => $user->no_hp,
                    'message'     => $pesan,
                    'countryCode' => '62', // Konversi 08 ke +628
                ]);

                if ($response->successful()) {
                    $sewa->update(['notifikasi_terkirim' => true]);
                    $this->info("Berhasil mengirim pengingat ke {$user->no_hp} (Unit: {$kodeUnit})");
                    Log::info("Auto-Remind Termin 2 Sent: {$user->no_hp}");
                } else {
                    $this->error("Gagal mengirim ke {$user->no_hp}: " . $response->body());
                }
            } catch (\Exception $e) {
                $this->error("Kesalahan teknis: " . $e->getMessage());
                Log::error("Gagal Auto-Remind Termin 2: " . $e->getMessage());
            }
        }

        $this->info("Proses pengiriman pengingat selesai.");
    }
}
