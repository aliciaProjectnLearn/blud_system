<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DetailKomponenServisTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('detail_komponen_servis')->insert([
            [
                'pembayaran_ac_id' => 1,
                'komponen_id'      => 1,
                'jumlah'           => 1,
                'subtotal'         => 50000,
            ],
            [
                'pembayaran_ac_id' => 2,
                'komponen_id'      => 2,
                'jumlah'           => 2,
                'subtotal'         => 50000,
            ],
            [
                'pembayaran_ac_id' => 3,
                'komponen_id'      => 3,
                'jumlah'           => 1,
                'subtotal'         => 150000,
            ],
        ]);
    }
}