<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('roles_users')->truncate();
        DB::table('users')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        DB::table('users')->insert([
            [
                'name'              => 'Super Admin',
                'username'          => 'super_admin',
                'nama_lengkap'      => 'Super Admin',
                'no_hp'             => '081234567890',
                'email'             => 'superadmin@example.com',
                'email_verified_at' => now(),
                'password'          => Hash::make('password'),
                'remember_token'    => null,
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            [
                'name'              => 'Admin Futsal',
                'username'          => 'admin_futsal',
                'nama_lengkap'      => 'Admin Futsal',
                'no_hp'             => '081234567891',
                'email'             => 'adminfutsal@example.com',
                'email_verified_at' => now(),
                'password'          => Hash::make('password'),
                'remember_token'    => null,
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            [
                'name'              => 'Admin Kantin',
                'username'          => 'admin_kantin',
                'nama_lengkap'      => 'Admin Kantin',
                'no_hp'             => '081234567892',
                'email'             => 'adminkantin@example.com',
                'email_verified_at' => now(),
                'password'          => Hash::make('password'),
                'remember_token'    => null,
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            [
                'name'              => 'Admin AC',
                'username'          => 'admin_ac',
                'nama_lengkap'      => 'Admin AC',
                'no_hp'             => '081234567893',
                'email'             => 'adminac@example.com',
                'email_verified_at' => now(),
                'password'          => Hash::make('password'),
                'remember_token'    => null,
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            [
                'name'              => 'Admin Servis',
                'username'          => 'admin_servis',
                'nama_lengkap'      => 'Admin Servis',
                'no_hp'             => '081234567893',
                'email'             => 'adminservis@example.com',
                'email_verified_at' => now(),
                'password'          => Hash::make('password'),
                'remember_token'    => null,
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            [
                'name'              => 'Pelanggan',
                'username'          => 'pelanggan',
                'nama_lengkap'      => 'Pelanggan',
                'no_hp'             => '081234567894',
                'email'             => 'pelanggan@example.com',
                'email_verified_at' => now(),
                'password'          => Hash::make('password'),
                'remember_token'    => null,
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            [
                'name'              => 'Pelanggan 2',
                'username'          => 'pelanggan2',
                'nama_lengkap'      => 'Pelanggan 2',
                'no_hp'             => '081234567895',
                'email'             => 'pelanggan2@example.com',
                'email_verified_at' => now(),
                'password'          => Hash::make('password'),
                'remember_token'    => null,
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            [
                'name'              => 'Pelanggan 3',
                'username'          => 'pelanggan3',
                'nama_lengkap'      => 'Pelanggan 3',
                'no_hp'             => '081234567896',
                'email'             => 'pelanggan3@example.com',
                'email_verified_at' => now(),
                'password'          => Hash::make('password'),
                'remember_token'    => null,
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            [
                'name'              => 'Pelanggan 4',
                'username'          => 'pelanggan4',
                'nama_lengkap'      => 'Pelanggan 4',
                'no_hp'             => '081234567897',
                'email'             => 'pelanggan4@example.com',
                'email_verified_at' => now(),
                'password'          => Hash::make('password'),
                'remember_token'    => null,
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            [
                'name'              => 'Teknisi 1',
                'username'          => 'teknisi1',
                'nama_lengkap'      => 'Teknisi Satu',
                'no_hp'             => '081234567898',
                'email'             => 'teknisi1@example.com',
                'email_verified_at' => now(),
                'password'          => Hash::make('password'),
                'remember_token'    => null,
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            [
                'name'              => 'Teknisi 2',
                'username'          => 'teknisi2',
                'nama_lengkap'      => 'Teknisi Dua',
                'no_hp'             => '081234567899',
                'email'             => 'teknisi2@example.com',
                'email_verified_at' => now(),
                'password'          => Hash::make('password'),
                'remember_token'    => null,
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            [
                'name'              => 'Teknisi 3',
                'username'          => 'teknisi3',
                'nama_lengkap'      => 'Teknisi Tiga',
                'no_hp'             => '081234567800',
                'email'             => 'teknisi3@example.com',
                'email_verified_at' => now(),
                'password'          => Hash::make('password'),
                'remember_token'    => null,
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
        ]);
    }
}
