<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ruko', function (Blueprint $table) {
            // Ditambahkan setelah id, unique agar tidak duplikat
            $table->string('kode_unit', 10)->unique()->nullable()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('ruko', function (Blueprint $table) {
            $table->dropUnique(['kode_unit']);
            $table->dropColumn('kode_unit');
        });
    }
};
