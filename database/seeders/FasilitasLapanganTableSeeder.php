<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FasilitasLapanganTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('fasilitas_lapangan')->insert([
            [
                'lapangan_id' => 1,
                'nama'        => 'Kamar Mandi Dalam Pria dan wanita',
                'deskripsi'   => 'Kamar Mandi dalam untuk pria dan wanita dengan fasilitas yang bersih',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}