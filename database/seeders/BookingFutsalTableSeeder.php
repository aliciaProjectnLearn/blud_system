<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookingFutsalTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('booking_futsal')->insert([
            // Pelanggan (id:5) - reguler, booking kemarin → ACTIVE
            [
                'booking_id'        => 1,
                'user_id'           => 5,
                'lapangan_id'       => 1,
                'tgl_main'          => now()->subDays(1)->toDateString(),
                'jam_mulai'         => '15:00:00',
                'jam_mulai_efektif' => '15:00:00',
                'jam_selesai'       => '16:00:00',
                'durasi_main'       => 1,
                'jenis_pembayaran'  => 'reguler',
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            // Pelanggan 2 (id:6) - membership (tidak muncul di pelanggan reguler)
            [
                'booking_id'        => 2,
                'user_id'           => 6,
                'lapangan_id'       => 1,
                'tgl_main'          => now()->addDays(2)->toDateString(),
                'jam_mulai'         => '16:00:00',
                'jam_mulai_efektif' => '16:00:00',
                'jam_selesai'       => '18:00:00',
                'durasi_main'       => 2,
                'jenis_pembayaran'  => 'membership',
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            // Pelanggan 3 (id:7) - membership (tidak muncul di pelanggan reguler)
            [
                'booking_id'        => 3,
                'user_id'           => 7,
                'lapangan_id'       => 1,
                'tgl_main'          => now()->addDays(3)->toDateString(),
                'jam_mulai'         => '19:00:00',
                'jam_mulai_efektif' => '19:00:00',
                'jam_selesai'       => '20:00:00',
                'durasi_main'       => 1,
                'jenis_pembayaran'  => 'membership',
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            // Pelanggan 4 (id:8) - reguler, booking 35 hari lalu → INACTIVE
            [
                'booking_id'        => null,
                'user_id'           => 8,
                'lapangan_id'       => 1,
                'tgl_main'          => now()->subDays(35)->toDateString(),
                'jam_mulai'         => '13:00:00',
                'jam_mulai_efektif' => '13:00:00',
                'jam_selesai'       => '14:00:00',
                'durasi_main'       => 1,
                'jenis_pembayaran'  => 'reguler',
                'created_at'        => now()->subDays(35),
                'updated_at'        => now()->subDays(35),
            ],
        ]);
    }
}
