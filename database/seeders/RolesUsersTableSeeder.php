<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesUsersTableSeeder extends Seeder
{
    public function run(): void
    {
        // Asumsi: id user 1 = Super Admin, 2 = Admin Futsal, 3 = Admin Kantin, 4 = Admin AC, 5-7 = Pelanggan
        // Asumsi: id role 1 = Superadmin, 2 = Adminfutsal, 3 = Adminkantin, 4 = Adminac, 5 = Pelanggan

        DB::table('roles_users')->insert([
            ['user_id' => 1, 'role_id' => 1, 'created_at' => now(), 'updated_at' => now()], // Super Admin punya role Superadmin
            ['user_id' => 2, 'role_id' => 2, 'created_at' => now(), 'updated_at' => now()], // Admin Futsal punya role Adminfutsal
            ['user_id' => 3, 'role_id' => 3, 'created_at' => now(), 'updated_at' => now()], // Admin Kantin punya role Adminkantin
            ['user_id' => 4, 'role_id' => 4, 'created_at' => now(), 'updated_at' => now()], // Admin AC punya role Adminac
            ['user_id' => 5, 'role_id' => 5, 'created_at' => now(), 'updated_at' => now()], // Pelanggan 1
            ['user_id' => 6, 'role_id' => 5, 'created_at' => now(), 'updated_at' => now()], // Pelanggan 2
            ['user_id' => 7, 'role_id' => 5, 'created_at' => now(), 'updated_at' => now()], // Pelanggan 3
            
        ]);
    }
}