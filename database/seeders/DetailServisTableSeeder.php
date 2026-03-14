<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DetailServisTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('detail_servis')->insert([
            [
                'booking_id'     => 1,
                'jasa_cek'       => 'Pembersihan',
                'tambah_freon'   => 0.5,
                'berat_freon_kg' => 0.5,
                'catatan'        => 'Freon ditambah 0.5 kg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'booking_id'     => 2,
                'jasa_cek'       => 'Perbaikan',
                'tambah_freon'   => 0,
                'berat_freon_kg' => 0,
                'catatan'        => 'Ganti kapasitor',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'booking_id'     => 3,
                'jasa_cek'       => 'Servis rutin',
                'tambah_freon'   => 0,
                'berat_freon_kg' => 0,
                'catatan'        => 'Bersih-bersih',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}