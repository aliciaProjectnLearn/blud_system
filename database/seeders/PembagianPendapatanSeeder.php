<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PembagianPendapatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
            DB::table('pembagian_pendapatan')->insert([
                
            // AC: jurusan 40%, aplikasi 10%, blud 40%, bersih 10%
            ['sistem' => 'ac', 'penerima' => 'jurusan',  'persentase' => 40.00, 'created_at' => now(), 'updated_at' => now()],
            ['sistem' => 'ac', 'penerima' => 'aplikasi', 'persentase' => 10.00, 'created_at' => now(), 'updated_at' => now()],
            ['sistem' => 'ac', 'penerima' => 'blud',     'persentase' => 40.00, 'created_at' => now(), 'updated_at' => now()],
            ['sistem' => 'ac', 'penerima' => 'bersih',   'persentase' => 10.00, 'created_at' => now(), 'updated_at' => now()],

            // Kantin: aplikasi 10%, blud 50%, bersih 40%
            ['sistem' => 'kantin', 'penerima' => 'aplikasi', 'persentase' => 10.00, 'created_at' => now(), 'updated_at' => now()],
            ['sistem' => 'kantin', 'penerima' => 'blud',     'persentase' => 50.00, 'created_at' => now(), 'updated_at' => now()],
            ['sistem' => 'kantin', 'penerima' => 'bersih',   'persentase' => 40.00, 'created_at' => now(), 'updated_at' => now()],

            // Futsal: aplikasi 10%, blud 50%, bersih 40%
            ['sistem' => 'futsal', 'penerima' => 'aplikasi', 'persentase' => 10.00, 'created_at' => now(), 'updated_at' => now()],
            ['sistem' => 'futsal', 'penerima' => 'blud',     'persentase' => 50.00, 'created_at' => now(), 'updated_at' => now()],
            ['sistem' => 'futsal', 'penerima' => 'bersih',   'persentase' => 40.00, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
