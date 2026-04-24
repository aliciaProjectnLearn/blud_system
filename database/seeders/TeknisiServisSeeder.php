<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class TeknisiServisSeeder extends Seeder
{
    public function run(): void
    {
        // ── Data Teknisi Servis ──────────────────────────────
        $teknisis = [
            [
                'name'         => 'Teknisi Motor 1',
                'username'     => 'teknisi_motor1',
                'nama_lengkap' => 'Teknisi Motor Satu',
                'no_hp'        => '081111111101',
                'email'        => 'teknisimotor1@servis.com',
                'password'     => Hash::make('teknisi123'),
                'role'         => 'Teknisi Motor',
            ],
            [
                'name'         => 'Teknisi Motor 2',
                'username'     => 'teknisi_motor2',
                'nama_lengkap' => 'Teknisi Motor Dua',
                'no_hp'        => '081111111102',
                'email'        => 'teknisimotor2@servis.com',
                'password'     => Hash::make('teknisi123'),
                'role'         => 'Teknisi Motor',
            ],
            [
                'name'         => 'Teknisi Mobil 1',
                'username'     => 'teknisi_mobil1',
                'nama_lengkap' => 'Teknisi Mobil Satu',
                'no_hp'        => '081111111103',
                'email'        => 'teknisimobil1@servis.com',
                'password'     => Hash::make('teknisi123'),
                'role'         => 'Teknisi Mobil',
            ],
            [
                'name'         => 'Teknisi Mobil 2',
                'username'     => 'teknisi_mobil2',
                'nama_lengkap' => 'Teknisi Mobil Dua',
                'no_hp'        => '081111111104',
                'email'        => 'teknisimobil2@servis.com',
                'password'     => Hash::make('teknisi123'),
                'role'         => 'Teknisi Mobil',
            ],
        ];

        foreach ($teknisis as $data) {
            // Cek apakah email sudah ada, skip jika sudah
            $existingUser = DB::table('users')
                ->where('email', $data['email'])
                ->first();

            if ($existingUser) {
                $this->command->warn("Skip: {$data['email']} sudah ada.");
                continue;
            }

            // Insert user
            $userId = DB::table('users')->insertGetId([
                'name'         => $data['name'],
                'username'     => $data['username'],
                'nama_lengkap' => $data['nama_lengkap'],
                'no_hp'        => $data['no_hp'],
                'email'        => $data['email'],
                'password'     => $data['password'],
                'status_aktif' => 1,
                'status_futsal'=> 'active',
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);

            // Ambil role_id
            $roleId = DB::table('roles')
                ->where('nama', $data['role'])
                ->value('id');

            if (!$roleId) {
                $this->command->error("Role '{$data['role']}' tidak ditemukan! Pastikan roles sudah ada di tabel.");
                continue;
            }

            // Assign role ke user
            DB::table('roles_users')->insert([
                'user_id' => $userId,
                'role_id' => $roleId,
            ]);

            $this->command->info("Berhasil: {$data['email']} → {$data['role']}");
        }
    }
}