<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kategori', function (Blueprint $table) {
            $table->string('tipe')->default('umum')->after('nama');
        });

        // Set tipe AC untuk kategori existing yang relevan
        DB::table('kategori')->where('nama', 'LIKE', '%AC%')->update(['tipe' => 'ac']);
        
        // Set tipe Kantin untuk kategori existing yang relevan
        DB::table('kategori')->where('nama', 'LIKE', '%Kantin%')->update(['tipe' => 'kantin']);
    }

    public function down(): void
    {
        Schema::table('kategori', function (Blueprint $table) {
            $table->dropColumn('tipe');
        });
    }
};
