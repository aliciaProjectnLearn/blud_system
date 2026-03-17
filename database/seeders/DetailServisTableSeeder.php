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
                'item'      => 'Cek AC',
                'quantity'  => '2.00',
                'catatan'        => 'Freon ditambah 0.5 kg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'booking_id'     => 2,
                'item'      => 'Perbaikan',
                'quantity'  => '1.00',
                'catatan'        => 'Freon ditambah 0.5 kg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'booking_id'     => 3,
                'item'      => 'Servis rutin',
                'quantity'  => '2.00',
                'catatan'        => 'Bersih-bersih',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}