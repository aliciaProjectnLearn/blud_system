<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class KasirFutsalUserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'kasir.futsal@blud.com'],
            [
                'name'              => 'Kasir Futsal',
                'username'          => 'kasir_futsal',
                'nama_lengkap'      => 'Kasir Futsal',
                'no_hp'             => '081234567800',
                'email_verified_at' => now(),
                'password'          => Hash::make('password'),
            ]
        );

        $role = Role::where('nama', 'kasirfutsal')->first();

        if ($role && !$user->roles->contains($role->id)) {
            $user->roles()->attach($role->id);
        }
    }
}
