<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookingFutsalTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('booking_futsal')->insert([
            [
                'booking_id'          => 1,
                'user_id'             => 5,
                'lapangan_id'         => 1,
                'tgl_main'            => now()->addDays(1)->toDateString(),
                'jam_mulai'           => '15:00:00',
                'jam_mulai_efektif'   => '15:00:00',
                'jam_selesai'         => '16:00:00',
                'durasi_main'         => 1,
                'created_at' => now(),
                 'updated_at' => now()
            ],
            [
                'booking_id'          => 2,
                'user_id'             => 5,
                'lapangan_id'         => 1,
                'tgl_main'            => now()->addDays(2)->toDateString(),
                'jam_mulai'           => '16:00:00',
                'jam_mulai_efektif'   => '16:00:00',
                'jam_selesai'         => '18:00:00',
                'durasi_main'         => 2,
                'created_at' => now(),
                 'updated_at' => now()
            ],
            [
                'booking_id'          => 3,
                'user_id'             => 5,
                'lapangan_id'         => 1,
                'tgl_main'            => now()->addDays(3)->toDateString(),
                'jam_mulai'           => '19:00:00',
                'jam_mulai_efektif'   => '19:00:00',
                'jam_selesai'         => '20:00:00',
                'durasi_main'         => 1,
                'created_at' => now(),
                 'updated_at' => now()
            ],
        ]);
    }
}