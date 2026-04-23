<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class KasirUserSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // 1. Pastikan role 'kasir' ada (cek nama case-insensitive)
        $role = DB::table('roles')
            ->whereRaw('LOWER(nama) = ?', ['kasir'])
            ->first();

        if (!$role) {
            $roleId = DB::table('roles')->insertGetId([
                'nama'       => 'kasir',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $this->command->info("Role 'kasir' dibuat (ID: {$roleId})");
        } else {
            $roleId = $role->id;
            $this->command->info("Role '{$role->nama}' sudah ada (ID: {$roleId})");
        }

        // 2. Buat atau perbarui user kasir
        $existing = DB::table('users')->where('email', 'kasir@bengkel.com')->first();

        if (!$existing) {
            $kasirId = DB::table('users')->insertGetId([
                'name'         => 'Kasir',
                'username'     => 'kasir_bengkel',
                'email'        => 'kasir@bengkel.com',
                'password'     => Hash::make('password'),
                'nama_lengkap' => 'Kasir',
                'no_hp'        => '08999999999',
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);
            $this->command->info("User kasir dibuat (ID: {$kasirId})");
        } else {
            $kasirId = $existing->id;
            $this->command->info("User kasir sudah ada (ID: {$kasirId})");
        }

        // 3. Assign role ke user (idempotent)
        $pivot = DB::table('roles_users')
            ->where('user_id', $kasirId)
            ->where('role_id', $roleId)
            ->exists();

        if (!$pivot) {
            DB::table('roles_users')->insert([
                'user_id' => $kasirId,
                'role_id' => $roleId,
            ]);
            $this->command->info('Role kasir berhasil di-assign ke user!');
        } else {
            $this->command->info('Role sudah ter-assign sebelumnya.');
        }

        $this->command->info('');
        $this->command->info('=== AKUN KASIR ===');
        $this->command->info('Email    : kasir@bengkel.com');
        $this->command->info('Password : password');
        $this->command->info('URL      : http://127.0.0.1:8000/login');
        $this->command->info('==================');
    }
}
