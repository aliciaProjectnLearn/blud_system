<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * PembayaranRukoTableSeeder
 *
 * Membuat 2 record pembayaran (termin 1 & 2) untuk setiap sewa_ruko.
 * Jumlah per termin = harga_sewa_tahunan / 2 (diambil langsung dari kolom sewa_ruko).
 *
 * Aturan konsistensi status:
 *  - Sewa 'aktif'   → termin1 = lunas, termin2 = menunggu (atau lunas jika bulan ke-6 sudah lewat)
 *  - Sewa 'pending' → termin1 = menunggu (atau verifikasi), termin2 = menunggu
 *  - Sewa 'selesai' → termin1 = lunas, termin2 = lunas
 *
 * Tipe pembayaran: id dari tabel tipe_pembayaran
 *  - Asumsi: id=1 = Tunai, id=2 = QRIS (sesuai TipePembayaranTableSeeder)
 */
class PembayaranRukoTableSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil semua sewa beserta harga dan tanggal mulai
        $sewas = DB::table('sewa_ruko')
            ->join('ruko', 'sewa_ruko.ruko_id', '=', 'ruko.id')
            ->select(
                'sewa_ruko.id         as sewa_id',
                'sewa_ruko.booking_id as booking_id',
                'sewa_ruko.tgl_mulai  as tgl_mulai',
                'sewa_ruko.status     as status_sewa',
                'ruko.harga           as harga',
                'ruko.kode_unit       as kode_unit'
            )
            ->get();

        if ($sewas->isEmpty()) {
            $this->command->warn('PembayaranRukoTableSeeder: tidak ada data sewa_ruko, seeder dilewati.');
            return;
        }

        // Ambil id tipe pembayaran
        $idTunai = DB::table('tipe_pembayaran')->where('nama', 'Tunai')->value('id') ?? 1;
        $idQris  = DB::table('tipe_pembayaran')->where('nama', 'QRIS')->value('id') ?? 2;

        $now = Carbon::now();
        $counter = 0;

        foreach ($sewas as $sewa) {
            $harga          = (int) $sewa->harga;
            $termin1Amount  = intdiv($harga, 2);
            $termin2Amount  = $harga - $termin1Amount;

            $tglMulai       = Carbon::parse($sewa->tgl_mulai);
            $tglJatuhTempo1 = $tglMulai->copy();
            $tglJatuhTempo2 = $tglMulai->copy()->addMonths(6);
            $bulan6Sudah    = $now->greaterThanOrEqualTo($tglJatuhTempo2);

            // ── Tentukan status termin berdasarkan status sewa ──────
            switch ($sewa->status_sewa) {

                case 'aktif':
                    // Termin 1 selalu lunas
                    $statusT1  = 'lunas';
                    $tglBayarT1 = $tglJatuhTempo1->copy()->addDays(1)->format('Y-m-d');
                    $kwtT1      = 'KWT-' . $tglMulai->format('Y') . '-' . $sewa->sewa_id . '-1';
                    $tipeT1     = $idTunai;

                    // Termin 2: lunas jika >6 bulan, menunggu jika belum
                    $statusT2   = $bulan6Sudah ? 'lunas' : 'menunggu';
                    $tglBayarT2 = $bulan6Sudah ? $tglJatuhTempo2->copy()->addDays(2)->format('Y-m-d') : null;
                    $kwtT2      = $bulan6Sudah ? 'KWT-' . $tglMulai->format('Y') . '-' . $sewa->sewa_id . '-2' : null;
                    $tipeT2     = $idQris;
                    break;

                case 'pending':
                    // Termin 1: verifikasi jika tgl_mulai sudah lewat > 1 hari, menunggu jika baru saja
                    $tglMulaiParsed = Carbon::parse($sewa->tgl_mulai);
                    $sudahLewat     = $tglMulaiParsed->isPast() && $now->diffInDays($tglMulaiParsed) >= 1;
                    $statusT1  = $sudahLewat ? 'verifikasi' : 'menunggu';
                    $tglBayarT1 = $sudahLewat ? $tglMulaiParsed->format('Y-m-d') : null;
                    $kwtT1      = null;
                    $tipeT1     = $idTunai;

                    // Termin 2 selalu menunggu
                    $statusT2   = 'menunggu';
                    $tglBayarT2 = null;
                    $kwtT2      = null;
                    $tipeT2     = $idTunai;
                    break;

                case 'selesai':
                default:
                    // Keduanya lunas
                    $statusT1   = 'lunas';
                    $tglBayarT1 = $tglJatuhTempo1->copy()->addDays(1)->format('Y-m-d');
                    $kwtT1      = 'KWT-' . $tglMulai->format('Y') . '-' . $sewa->sewa_id . '-1';
                    $tipeT1     = $idTunai;

                    $statusT2   = 'lunas';
                    $tglBayarT2 = $tglJatuhTempo2->copy()->addDays(2)->format('Y-m-d');
                    $kwtT2      = 'KWT-' . $tglMulai->format('Y') . '-' . $sewa->sewa_id . '-2';
                    $tipeT2     = $idQris;
                    break;
            }

            // ── Insert Termin 1 ──────────────────────────────────────
            DB::table('pembayaran_ruko')->insert([
                'sewa_ruko_id'       => $sewa->sewa_id,
                'booking_id'         => $sewa->booking_id,
                'tipe_pembayaran_id' => $tipeT1,
                'termin'             => 1,
                'tgl_jatuh_tempo'    => $tglJatuhTempo1->format('Y-m-d'),
                'jumlah_tagihan'     => $termin1Amount,
                'tgl_bayar'          => $tglBayarT1,
                'status'             => $statusT1,
                'no_kwitansi'        => $kwtT1,
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);

            // ── Insert Termin 2 ──────────────────────────────────────
            DB::table('pembayaran_ruko')->insert([
                'sewa_ruko_id'       => $sewa->sewa_id,
                'booking_id'         => $sewa->booking_id,
                'tipe_pembayaran_id' => $tipeT2,
                'termin'             => 2,
                'tgl_jatuh_tempo'    => $tglJatuhTempo2->format('Y-m-d'),
                'jumlah_tagihan'     => $termin2Amount,
                'tgl_bayar'          => $tglBayarT2,
                'status'             => $statusT2,
                'no_kwitansi'        => $kwtT2,
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);

            $this->command->line(
                "  → [{$sewa->kode_unit}] T1:{$statusT1} | T2:{$statusT2}" .
                " | Rp " . number_format($termin1Amount, 0, ',', '.') . " each"
            );
            $counter++;
        }

        $this->command->info("PembayaranRukoTableSeeder: {$counter} sewa × 2 record = " . ($counter * 2) . " pembayaran di-seed.");
    }
}
