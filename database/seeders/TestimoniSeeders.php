<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestimoniSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'nama'    => 'Budi Santoso',
                'peran'   => 'Pengguna Futsal',
                'bintang' => 5,
                'isi'     => 'Booking lapangan sekarang jauh lebih mudah! Tidak perlu telepon dulu, cukup pilih jadwal yang kosong langsung dari HP.',
                'tampil'  => true,
            ],
            [
                'nama'    => 'Siti Rahayu',
                'peran'   => 'Penyewa Kantin',
                'bintang' => 5,
                'isi'     => 'Proses pengajuan sewa unit kantin sangat transparan. Semua dokumen bisa diupload dan dipantau statusnya secara langsung.',
                'tampil'  => true,
            ],
            [
                'nama'    => 'Ahmad Fauzi',
                'peran'   => 'Pengguna Servis AC',
                'bintang' => 4,
                'isi'     => 'Teknisinya profesional dan datang tepat waktu. Jadwal kunjungan bisa dipilih sendiri, sangat membantu dan tidak ribet!',
                'tampil'  => true,
            ],
        ];

        foreach ($data as $item) {
            DB::table('testimoni')->insert(array_merge($item, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
