<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booking_servis', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->change();
            $table->string('nama_pemesan', 255)->nullable()->after('user_id');
            $table->string('no_hp', 20)->nullable()->after('nama_pemesan');
            $table->dropColumn('tipe_kendaraan');
        });
    }

    public function down(): void
    {
        Schema::table('booking_servis', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
            $table->dropColumn(['nama_pemesan', 'no_hp']);
            $table->enum('tipe_kendaraan', ['motor', 'mobil'])->default('motor');
        });
    }
};
