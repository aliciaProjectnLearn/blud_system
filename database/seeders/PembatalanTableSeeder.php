<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PembatalanTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('pembatalan')->insert([
            [
                'booking_id'       => 3,
                'alasan'           => 'Hujan deras',
                'tgl_pembatalan'   => now()->subDays(1)->toDateString(),
            ],
            [
                'booking_id'       => 6,
                'alasan'           => 'Perubahan jadwal',
                'tgl_pembatalan'   => now()->subDays(2)->toDateString(),
            ],
            [
                'booking_id'       => 9,
                'alasan'           => 'Sakit',
                'tgl_pembatalan'   => now()->subDays(3)->toDateString(),
            ],
        ]);
    }
}