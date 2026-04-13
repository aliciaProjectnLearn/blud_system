<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * PenyewaSeeder
 *
 * Membuat data penyewa yang berelasi ke user pelanggan yang sudah ada
 * (dibuat oleh UsersTableSeeder: user_id 5 = Pelanggan, 6 = Pelanggan 2,
 *  7 = Pelanggan 3, 8 = Pelanggan 4).
 *
 * !! Tidak membuat user baru — pakai user yang sudah ada agar tidak duplikasi. !!
 */
class PenyewaSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil id user pelanggan berdasarkan username (lebih aman daripada hardcoded id)
        $userIds = DB::table('users')
            ->whereIn('username', ['pelanggan', 'pelanggan2', 'pelanggan3', 'pelanggan4'])
            ->pluck('id', 'username');

        $data = [
            [
                'username'   => 'pelanggan',
                'nama_usaha' => 'Warung Nasi Bu Sari',
                'alamat'     => 'Jl. Merdeka No. 10, Cirebon',
                'nik'        => '3274010101800001',
            ],
            [
                'username'   => 'pelanggan2',
                'nama_usaha' => 'Kantin Sehat Pak Andi',
                'alamat'     => 'Jl. Sudirman No. 25, Cirebon',
                'nik'        => '3274010202810002',
            ],
            [
                'username'   => 'pelanggan3',
                'nama_usaha' => 'Fotocopy & ATK Maju',
                'alamat'     => 'Jl. Diponegoro No. 5, Cirebon',
                'nik'        => '3274010303820003',
            ],
            [
                'username'   => 'pelanggan4',
                'nama_usaha' => 'Toko Kelontong Barokah',
                'alamat'     => 'Jl. Ahmad Yani No. 88, Cirebon',
                'nik'        => '3274010404830004',
            ],
        ];

        foreach ($data as $d) {
            $userId = $userIds[$d['username']] ?? null;

            if (!$userId) {
                $this->command->warn("PenyewaSeeder: user '{$d['username']}' tidak ditemukan, dilewati.");
                continue;
            }

            DB::table('penyewa')->updateOrInsert(
                ['user_id' => $userId],
                [
                    'nama_usaha' => $d['nama_usaha'],
                    'alamat'     => $d['alamat'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            // Update NIK di tabel users agar konsisten
            DB::table('users')->where('id', $userId)->update(['nik' => $d['nik']]);
        }

        $this->command->info('PenyewaSeeder: ' . count($data) . ' penyewa berhasil di-seed.');
    }
}
