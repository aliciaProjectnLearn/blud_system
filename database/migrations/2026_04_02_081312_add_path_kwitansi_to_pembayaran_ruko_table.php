<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembayaran_ruko', function (Blueprint $table) {
            $table->string('path_kwitansi')->nullable()->after('no_kwitansi');
        });
    }

    public function down(): void
    {
        Schema::table('pembayaran_ruko', function (Blueprint $table) {
            $table->dropColumn('path_kwitansi');
        });
    }
};
