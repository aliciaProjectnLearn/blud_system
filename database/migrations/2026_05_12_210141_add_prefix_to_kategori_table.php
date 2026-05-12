<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('kategori', function (Blueprint $col) {
            $col->string('prefix', 10)->nullable()->after('nama');
        });

        // Seed some default prefixes
        DB::table('kategori')->where('id', 1)->update(['prefix' => 'KNT']); // Kantin Besar
        DB::table('kategori')->where('id', 2)->update(['prefix' => 'CNT']); // Kantin Container
        DB::table('kategori')->where('id', 3)->update(['prefix' => 'RKO']); // Ruko Depan
        DB::table('kategori')->where('id', 4)->update(['prefix' => 'SAC']); // Service AC
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kategori', function (Blueprint $col) {
            $col->dropColumn('prefix');
        });
    }
};
