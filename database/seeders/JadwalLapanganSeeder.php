<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use App\Models\Lapangan;
use App\Models\JadwalLapangan;

class JadwalLapanganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lapangans = Lapangan::all();

        if ($lapangans->isEmpty()) {
            $this->command->info('Tabel Lapangan kosong. Tidak ada jadwal yang di-generate.');
            return;
        }

        $jadwalData = [];
        $today = Carbon::today(); // Mulai dari hari ini

        // Looping untuk 8 hari (termasuk hari ini sampai 7 hari ke depan)
        for ($day = 0; $day <= 7; $day++) {
            $tanggal = $today->copy()->addDays($day)->format('Y-m-d');

            // Slot waktu per 1 jam dari 08:00 sampai 24:00
            for ($hour = 8; $hour <= 23; $hour++) {
                $jamMulai = sprintf('%02d:00:00', $hour);
                
                // Jika jam sudah 23, maka waktu selesainya di set ke 23:59:59
                // Karena beberapa versi database menolak strict format '24:00:00' untuk tipe TIME
                if ($hour == 23) {
                    $jamSelesai = '23:59:59';
                } else {
                    $jamSelesai = sprintf('%02d:00:00', $hour + 1);
                }

                foreach ($lapangans as $lapangan) {
                    $jadwalData[] = [
                        'lapangan_id' => $lapangan->id,
                        'tanggal'     => $tanggal,
                        'jam_mulai'   => $jamMulai,
                        'jam_selesai' => $jamSelesai,
                        'status'      => 'tersedia', // status default untuk slot ini
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ];
                }
            }
        }

        // Gunakan bulk insert agar jauh lebih performant dibanding create di dalam loop
        JadwalLapangan::insert($jadwalData);
        
        $this->command->info('Berhasil men-generate Jadwal Lapangan dari tanggal ' . $today->format('Y-m-d') . ' sampai ' . $today->copy()->addDays(7)->format('Y-m-d'));
    }
}
