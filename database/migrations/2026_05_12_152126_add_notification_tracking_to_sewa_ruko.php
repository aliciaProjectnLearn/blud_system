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
        Schema::table('sewa_ruko', function (Blueprint $table) {
            $table->boolean('notifikasi_termin2_sent')->default(false)->after('notifikasi_terkirim');
            $table->boolean('notifikasi_expiry_sent')->default(false)->after('notifikasi_termin2_sent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sewa_ruko', function (Blueprint $table) {
            $table->dropColumn(['notifikasi_termin2_sent', 'notifikasi_expiry_sent']);
        });
    }
};
