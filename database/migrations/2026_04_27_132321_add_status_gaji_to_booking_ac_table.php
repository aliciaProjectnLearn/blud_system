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
        Schema::table('booking_ac', function (Blueprint $table) {
            if (!Schema::hasColumn('booking_ac', 'status_gaji')) {
                $table->enum('status_gaji', ['belum_dibayar', 'dibayar'])->default('belum_dibayar')->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_ac', function (Blueprint $table) {
            $table->dropColumn('status_gaji');
        });
    }
};
