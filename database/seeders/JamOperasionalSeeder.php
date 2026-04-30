<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lapangan;
use App\Models\JamOperasionalLapangan;

class JamOperasionalSeeder extends Seeder
{
    public function run()
    {
        $lapangans = Lapangan::all();
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

        foreach ($lapangans as $lapangan) {
            foreach ($hariList as $hari) {
                JamOperasionalLapangan::firstOrCreate([
                    'lapangan_id' => $lapangan->id,
                    'hari' => $hari
                ], [
                    'jam_buka' => '07:00:00',
                    'jam_tutup' => '22:00:00',
                    'is_aktif' => true
                ]);
            }
        }
    }
}
