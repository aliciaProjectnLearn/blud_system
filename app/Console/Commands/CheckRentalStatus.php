<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckRentalStatus extends Command
{
    protected $signature = 'rental:check-status';
    protected $description = 'Periksa status sewa ruko (selesai) dan kirim notifikasi WhatsApp tagihan termin 2';

    public function handle()
    {
        $today = now()->format('Y-m-d');

        // 1. Update status 'selesai' jika melewati tgl_selesai
        $expiredRentals = \App\Models\SewaRuko::where('status', 'aktif')
            ->where('tgl_selesai', '<', $today)
            ->get();

        foreach ($expiredRentals as $sewa) {
            $sewa->update(['status' => 'selesai']);
            $sewa->ruko->update(['status_unit' => 'kosong']);
            $this->info("Sewa #{$sewa->id} telah diselesaikan.");
        }

        // 2. Notifikasi WhatsApp untuk Termin 2 (7 hari sebelum jatuh tempo)
        $sevenDaysFromNow = now()->addDays(7)->format('Y-m-d');
        $upcomingPayments = \App\Models\PembayaranRuko::where('termin', 2)
            ->where('status', 'menunggu')
            ->whereDate('tgl_jatuh_tempo', '<=', $sevenDaysFromNow)
            ->whereHas('sewaRuko', function($q) {
                $q->where('status', 'aktif')->where('notifikasi_terkirim', false);
            })
            ->with(['sewaRuko.penyewa.user', 'sewaRuko.ruko'])
            ->get();

        foreach ($upcomingPayments as $pay) {
            $sewa = $pay->sewaRuko;
            $user = $sewa->penyewa->user;

            $message = "Halo {$user->name}, ini adalah pengingat pembayaran sewa unit {$sewa->ruko->kode_unit} Termin 2 sebesar Rp" . number_format($pay->jumlah_tagihan, 0, ',', '.') . ". Jatuh tempo pada: " . \Carbon\Carbon::parse($pay->tgl_jatuh_tempo)->format('d M Y') . ". Silakan lakukan pembayaran melalui dashboard Anda.";
            
            // Mock WA notification (Fonnte/wa.me placeholder)
            \Log::info("WhatsApp Notification sent to {$user->no_hp}: {$message}");
            
            // Tandai notifikasi sudah terkirim agar tidak double
            $sewa->update(['notifikasi_terkirim' => true]);
            $this->info("Notifikasi Terkirim ke {$user->name} untuk Unit {$sewa->ruko->kode_unit}");
        }

        $this->info('Pemeriksaan status sewa selesai.');
    }
}
