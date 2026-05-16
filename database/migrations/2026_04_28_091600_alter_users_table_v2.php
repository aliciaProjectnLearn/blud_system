<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
            $table->string('password')->nullable()->change();
            
            if (Schema::hasColumn('users', 'nama_lengkap')) {
                $table->string('nama_lengkap')->nullable()->change();
            }
            if (Schema::hasColumn('users', 'nik')) {
                $table->string('nik')->nullable()->change();
            }
            if (Schema::hasColumn('users', 'alamat')) {
                $table->text('alamat')->nullable()->change();
            }
            
            // Kolom esensial tetap wajib
            $table->string('name')->nullable(false)->change();
            
            if (Schema::hasColumn('users', 'username')) {
                $table->string('username')->nullable(false)->change();
            }
            if (Schema::hasColumn('users', 'no_hp')) {
                $table->string('no_hp')->nullable(false)->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable(false)->change();
            $table->string('password')->nullable(false)->change();
            
            if (Schema::hasColumn('users', 'nama_lengkap')) {
                $table->string('nama_lengkap')->nullable(false)->change();
            }
        });
    }
};
