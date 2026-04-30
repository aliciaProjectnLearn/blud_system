<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name'     => 'Administrator',
                'email'    => 'admin@admin.com',
                'password' => Hash::make('password123'),
                'role'     => 'admin',
            ]
        );

        $this->command->info('✅ Admin berhasil dibuat!');
        $this->command->info('   Email    : admin@admin.com');
        $this->command->info('   Password : password123');
    }
}