<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PembayaranRukoTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('pembayaran_ruko')->insert([
            // Sewa 1 (booking_id: 10) - Termin 1 sudah verifikasi
            [
                'booking_id'         => 10,
                'tipe_pembayaran_id' => 1,
                'termin'             => 1,
                'tgl_jatuh_tempo'    => now()->addDay()->toDateString(),
                'jumlah_tagihan'     => 60000000,
                'tgl_bayar'          => now(),
                'status'             => 'verifikasi',
                'no_kwitansi'        => 'KWT/2026/03/0001',
                'created_at'         => now(),
                'updated_at'         => now(),
            ],
            // Sewa 1 (booking_id: 10) - Termin 2 menunggu
            [
                'booking_id'         => 10,
                'tipe_pembayaran_id' => 1,
                'termin'             => 2,
                'tgl_jatuh_tempo'    => now()->addMonths(6)->addDay()->toDateString(),
                'jumlah_tagihan'     => 60000000,
                'tgl_bayar'          => null,
                'status'             => 'menunggu',
                'no_kwitansi'        => null,
                'created_at'         => now(),
                'updated_at'         => now(),
            ],
            // Sewa 2 (booking_id: 11) - Termin 1 menunggak (jatuh tempo sudah lewat)
            [
                'booking_id'         => 11,
                'tipe_pembayaran_id' => 1,
                'termin'             => 1,
                'tgl_jatuh_tempo'    => now()->subDays(10)->toDateString(),
                'jumlah_tagihan'     => 120000000,
                'tgl_bayar'          => null,
                'status'             => 'menunggu',
                'no_kwitansi'        => null,
                'created_at'         => now(),
                'updated_at'         => now(),
            ],
            // Sewa 2 (booking_id: 11) - Termin 2 menunggu
            [
                'booking_id'         => 11,
                'tipe_pembayaran_id' => 1,
                'termin'             => 2,
                'tgl_jatuh_tempo'    => now()->subDays(10)->addMonths(6)->toDateString(),
                'jumlah_tagihan'     => 120000000,
                'tgl_bayar'          => null,
                'status'             => 'menunggu',
                'no_kwitansi'        => null,
                'created_at'         => now(),
                'updated_at'         => now(),
            ],
        ]);
    }
}
