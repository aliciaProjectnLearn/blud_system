<?php

namespace App\Console\Commands;

use App\Models\SewaRuko;
use App\Models\PembayaranRuko;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendLeaseExpiryNotification extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'kantin:notifikasi-masa-sewa';

    /**
     * The console command description.
     */
    protected $description = 'Kirim notifikasi WhatsApp H-30 untuk Pembayaran Termin 2 dan Akhir Masa Sewa';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $apiToken = config('services.fonnte.token');
        if (!$apiToken) {
            $this->error("Fonnte Token tidak ditemukan di config/services.php");
            return;
        }

        $this->info("Memulai proses pengecekan notifikasi...");

        // 1. PROSES NOTIFIKASI TERMIN 2 (H-30 Jatuh Tempo Pembayaran)
        $this->checkTermin2Reminders($apiToken);

        // 2. PROSES NOTIFIKASI AKHIR SEWA (H-30 Tanggal Selesai)
        $this->checkLeaseExpiryReminders($apiToken);

        $this->info("Seluruh proses pengiriman notifikasi selesai.");
    }

    /**
     * Cek dan kirim pengingat pembayaran Termin 2
     */
    private function checkTermin2Reminders($apiToken)
    {
        $targetDate = Carbon::today()->addDays(30)->toDateString();
        $this->info("--- Mengecek Pengingat Termin 2 (H-30 Jatuh Tempo: {$targetDate}) ---");

        $sewas = SewaRuko::where('status_sewa', 'aktif')
            ->where('tipe_pembayaran', '2_termin')
            ->where('notifikasi_termin2_sent', false)
            ->whereHas('pembayaran', function($q) use ($targetDate) {
                $q->where('termin_ke', 2)
                  ->where('status_pembayaran', 'pending')
                  ->whereDate('tgl_jatuh_tempo', $targetDate);
            })
            ->with(['ruko', 'pembayaran' => function($q) {
                $q->where('termin_ke', 2);
            }])
            ->get();

        if ($sewas->isEmpty()) {
            $this->info("Tidak ada pengingat Termin 2 untuk hari ini.");
            return;
        }

        foreach ($sewas as $sewa) {
            $termin2 = $sewa->pembayaran->first(); // Sudah difilter termin_ke = 2 di eager loading
            $this->sendTermin2WhatsApp($sewa, $termin2, $apiToken);
        }
    }

    /**
     * Cek dan kirim pengingat akhir masa sewa
     */
    private function checkLeaseExpiryReminders($apiToken)
    {
        $targetDate = Carbon::today()->addDays(30)->toDateString();
        $this->info("--- Mengecek Pengingat Akhir Sewa (H-30 Selesai: {$targetDate}) ---");

        $sewas = SewaRuko::where('status_sewa', 'aktif')
            ->where('tanggal_selesai_sewa', $targetDate)
            ->where('notifikasi_expiry_sent', false)
            ->with(['ruko'])
            ->get();

        if ($sewas->isEmpty()) {
            $this->info("Tidak ada pengingat akhir sewa untuk hari ini.");
            return;
        }

        foreach ($sewas as $sewa) {
            $this->sendExpiryWhatsApp($sewa, $apiToken);
        }
    }

    /**
     * Kirim WA untuk Termin 2
     */
    private function sendTermin2WhatsApp($sewa, $termin2, $apiToken)
    {
        $namaPenyewa = $sewa->nama_penyewa;
        $namaRuko = $sewa->ruko->nama_ruko ?? 'Unit Kantin';
        $kodeUnit = $sewa->ruko->kode_unit ?? '-';
        $nominal = number_format($termin2->jumlah_tagihan, 0, ',', '.');
        $tglJatuhTempo = Carbon::parse($termin2->tgl_jatuh_tempo)->translatedFormat('d F Y');

        $pesan = "Halo *{$namaPenyewa}* 👋\n\n";
        $pesan .= "Kami mengingatkan bahwa pembayaran sewa *Termin 2* untuk unit *{$namaRuko}* ({$kodeUnit}) akan jatuh tempo dalam *30 hari* lagi, tepatnya pada tanggal *{$tglJatuhTempo}*.\n\n";
        $pesan .= "💰 *Jumlah Tagihan:* Rp {$nominal}\n\n";
        $pesan .= "Mohon segera lakukan pelunasan melalui portal sewa untuk menghindari keterlambatan. Jika Anda sudah membayar, abaikan pesan ini.\n\n";
        $pesan .= "Terima kasih 🙏\n— Admin Kantin BLUD SMK";

        if ($this->dispatchWhatsApp($sewa->no_hp_snapshot, $pesan, $apiToken)) {
            $sewa->update(['notifikasi_termin2_sent' => true]);
            $this->info("Berhasil kirim pengingat Termin 2 ke {$sewa->nama_penyewa}");
        }
    }

    /**
     * Kirim WA untuk Akhir Masa Sewa
     */
    private function sendExpiryWhatsApp($sewa, $apiToken)
    {
        $namaPenyewa = $sewa->nama_penyewa;
        $namaRuko = $sewa->ruko->nama_ruko ?? 'Unit Kantin';
        $kodeUnit = $sewa->ruko->kode_unit ?? '-';
        $tglSelesai = Carbon::parse($sewa->tanggal_selesai_sewa)->translatedFormat('d F Y');

        $pesan = "Halo *{$namaPenyewa}* 👋\n\n";
        $pesan .= "Masa sewa unit *{$namaRuko}* ({$kodeUnit}) Anda akan segera berakhir dalam *30 hari* ke depan, pada tanggal *{$tglSelesai}*.\n\n";
        
        if ($sewa->tipe_pembayaran === '2_termin') {
            $pesan .= "Kami juga mengingatkan untuk memastikan seluruh kewajiban pembayaran (Termin 2) telah dilunasi.\n\n";
        }

        $pesan .= "Apakah Anda berminat untuk memperpanjang masa sewa unit tersebut untuk tahun berikutnya?\n";
        $pesan .= "Mohon segera sampaikan konfirmasi Anda kepada Admin Kantin agar unit tetap dapat dialokasikan untuk Anda.\n\n";
        $pesan .= "Terima kasih 🙏\n— Admin Kantin BLUD SMK";

        if ($this->dispatchWhatsApp($sewa->no_hp_snapshot, $pesan, $apiToken)) {
            $sewa->update(['notifikasi_expiry_sent' => true]);
            $this->info("Berhasil kirim pengingat Akhir Sewa ke {$sewa->nama_penyewa}");
        }
    }

    /**
     * Helper untuk kirim WhatsApp via Fonnte
     */
    private function dispatchWhatsApp($phone, $message, $apiToken)
    {
        $target = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($target, '0')) {
            $target = '62' . substr($target, 1);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $apiToken,
            ])->post('https://api.fonnte.com/send', [
                'target'      => $target,
                'message'     => $message,
                'countryCode' => '62',
            ]);

            $resBody = $response->json();
            if ($response->successful() && ($resBody['status'] ?? false) == true) {
                Log::info("WA Notification Sent: {$target}");
                return true;
            } else {
                Log::error("Gagal kirim WA ke {$target}: " . ($resBody['reason'] ?? $response->body()));
                return false;
            }
        } catch (\Exception $e) {
            Log::error("Error teknis Kirim WA: " . $e->getMessage());
            return false;
        }
    }
}
