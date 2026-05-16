<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('booking_ac', function (Blueprint $table) {
            $table->string('nama_pelanggan')->nullable()->after('user_id');
            $table->string('no_hp', 20)->nullable()->after('nama_pelanggan');
            $table->string('foto_hasil')->nullable()->after('status');
        });

        // Make user_id nullable
        DB::statement('ALTER TABLE booking_ac MODIFY user_id bigint unsigned NULL');
        
        // Add dibatalkan to enum status
        DB::statement("ALTER TABLE booking_ac MODIFY COLUMN status ENUM('menunggu', 'proses', 'selesai', 'dibatalkan') DEFAULT 'menunggu'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_ac', function (Blueprint $table) {
            $table->dropColumn(['nama_pelanggan', 'no_hp', 'foto_hasil']);
        });

        DB::statement('ALTER TABLE booking_ac MODIFY user_id bigint unsigned NOT NULL');
        DB::statement("ALTER TABLE booking_ac MODIFY COLUMN status ENUM('menunggu', 'proses', 'selesai') DEFAULT 'menunggu'");
    }
};
