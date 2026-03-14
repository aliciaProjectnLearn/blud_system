<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SewaRukoTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('sewa_ruko')->insert([
            [
                'booking_id'           => 7,
                'penyewa_id'           => 2,
                'ruko_id'              => 1,
                'tgl_mulai'            => now()->toDateString(),
                'tgl_selesai'          => now()->addYear()->toDateString(),
                'total_biaya_tahunan'  => 120000000,
                'no_mou'               => 'MOU/001',
                'status'               => 'disetujui',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'booking_id'           => 8,
                'penyewa_id'           => 2,
                'ruko_id'              => 2,
                'tgl_mulai'            => now()->addDays(10)->toDateString(),
                'tgl_selesai'          => now()->addYear()->addDays(10)->toDateString(),
                'total_biaya_tahunan'  => 180000000,
                'no_mou'               => 'MOU/002',
                'status'               => 'menunggu',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'booking_id'           => 9,
                'penyewa_id'           => 3,
                'ruko_id'              => 3,
                'tgl_mulai'            => now()->addDays(20)->toDateString(),
                'tgl_selesai'          => now()->addYear()->addDays(20)->toDateString(),
                'total_biaya_tahunan'  => 240000000,
                'no_mou'               => 'MOU/003',
                'status'               => 'menunggu',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}