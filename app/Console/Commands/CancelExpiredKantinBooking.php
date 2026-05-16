<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SewaRuko;
use App\Models\Ruko;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CancelExpiredKantinBooking extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kantin:cancel-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Otomatis membatalkan booking kantin yang tidak dibayar dalam 3x24 jam';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai pengecekan booking kantin kadaluarsa...');

        // Cari sewa ruko yang masih pending dan sudah lebih dari 3 hari
        $expiredSewas = SewaRuko::where('status_sewa', 'pending')
            ->where('created_at', '<=', now()->subDays(3))
            ->with(['ruko', 'pembayaran' => function($q) {
                $q->where('termin_ke', 1);
            }])
            ->get();

        if ($expiredSewas->isEmpty()) {
            $this->info('Tidak ada booking yang kadaluarsa.');
            return;
        }

        foreach ($expiredSewas as $sewa) {
            // Pastikan termin 1 memang belum dibayar
            $termin1 = $sewa->pembayaran->where('termin_ke', 1)->first();
            
            if ($termin1 && $termin1->status_pembayaran === 'pending') {
                $this->cancelBooking($sewa);
            }
        }

        $this->info('Proses pembatalan selesai.');
    }

    private function cancelBooking($sewa)
    {
        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            // 1. Update Status Sewa
            $sewa->update(['status_sewa' => 'dibatalkan']);

            // 2. Kembalikan Status Unit
            if ($sewa->ruko) {
                $sewa->ruko->update(['status' => 'tersedia']);
            }

            // 3. Catat Audit (Opsional jika ada service audit)
            if (class_exists('\App\Services\AuditService')) {
                \App\Services\AuditService::catat(
                    sistem: 'kantin',
                    tabelEntitas: 'sewa_ruko',
                    entitasId: $sewa->id,
                    aksi: 'booking_otomatis_dibatalkan',
                    dataLama: ['status_sewa' => 'pending'],
                    dataBaru: ['status_sewa' => 'dibatalkan'],
                    keterangan: 'Pembatalan otomatis oleh sistem karena melewati batas waktu pembayaran 3x24 jam'
                );
            }

            $this->sendCancellationWhatsApp($sewa);

            \Illuminate\Support\Facades\DB::commit();
            $this->warn("Booking ID {$sewa->id} ({$sewa->nama_penyewa}) telah dibatalkan.");
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            Log::error("Gagal membatalkan booking otomatis ID {$sewa->id}: " . $e->getMessage());
        }
    }

    private function sendCancellationWhatsApp($sewa)
    {
        $apiToken = config('services.fonnte.token');
        if (!$apiToken) return;

        $target = preg_replace('/[^0-9]/', '', $sewa->no_hp_snapshot);
        if (str_starts_with($target, '0')) {
            $target = '62' . substr($target, 1);
        }

        $pesan = "Halo *{$sewa->nama_penyewa}* 👋\n\n";
        $pesan .= "Mohon maaf, permohonan sewa unit *{$sewa->ruko->nama_ruko}* ({$sewa->ruko->kode_unit}) Anda telah *DIBATALKAN OTOMATIS* oleh sistem.\n\n";
        $pesan .= "Hal ini disebabkan karena pembayaran termin pertama belum kami terima dalam batas waktu 3x24 jam sejak pengajuan dilakukan.\n\n";
        $pesan .= "Unit tersebut kini telah tersedia kembali untuk disewa oleh pihak lain. Jika Anda masih berminat, silakan lakukan pengajuan ulang melalui portal kami.\n\n";
        $pesan .= "Terima kasih.\n— Admin Kantin BLUD";

        try {
            Http::withHeaders([
                'Authorization' => $apiToken,
            ])->post('https://api.fonnte.com/send', [
                'target'      => $target,
                'message'     => $pesan,
                'countryCode' => '62',
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal kirim WA pembatalan otomatis: ' . $e->getMessage());
        }
    }
}
