<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pengaturans', function (Blueprint $table) {
            $table->integer('harga_reguler_futsal')->default(75000)->after('jam_tutup');
            $table->integer('harga_event_futsal')->default(800000)->after('harga_reguler_futsal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengaturans', function (Blueprint $table) {
            $table->dropColumn(['harga_reguler_futsal', 'harga_event_futsal']);
        });
    }
};
