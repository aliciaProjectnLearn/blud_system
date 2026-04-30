<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: tambah kolom dulu semua nullable
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->after('name');
            $table->string('nama_lengkap')->nullable()->after('username');
            $table->string('no_hp')->nullable()->after('nama_lengkap');
        });

        // Step 2: isi nilai default untuk row yang sudah ada agar tidak kosong
        DB::table('users')->whereNull('username')->update([
            'username'     => DB::raw('CONCAT("user_", id)'),
            'nama_lengkap' => DB::raw('name'),
            'no_hp'        => '000000000000',
        ]);

        // Step 3: baru pasang unique & ubah jadi not null
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable(false)->unique()->change();
            $table->string('nama_lengkap')->nullable(false)->change();
            $table->string('no_hp')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_username_unique');
            $table->dropColumn(['username', 'nama_lengkap', 'no_hp']);
        });
    }
};
