<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PembayaranRukoTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('pembayaran_ruko')->insert([
            [
                'booking_id'          => 7,
                'tipe_pembayaran_id'  => 1,
                'termin'              => 1,
                'tgl_jatuh_tempo'     => now()->addDays(30)->toDateString(),
                'jumlah_tagihan'      => 60000000,
                'tgl_bayar'           => now(),
                'status'              => 'verifikasi',
                'no_kwitansi'         => 'KW/001',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'booking_id'          => 8,
                'tipe_pembayaran_id'  => 1,
                'termin'              => 2,
                'tgl_jatuh_tempo'     => now()->addDays(60)->toDateString(),
                'jumlah_tagihan'      => 60000000,
                'tgl_bayar'           => null,
                'status'              => 'menunggu',
                'no_kwitansi'         => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
                 [
                    'booking_id'          => 9,
                    'tipe_pembayaran_id'  => 1,
                    'termin'              => 1,
                    'tgl_jatuh_tempo'     => now()->addDays(30)->toDateString(),
                    'jumlah_tagihan'      => 120000000,
                    'tgl_bayar'           => now(),
                    'status'              => 'dibatalkan',
                    'no_kwitansi'         => 'KW/002',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
        ]);
    }
}