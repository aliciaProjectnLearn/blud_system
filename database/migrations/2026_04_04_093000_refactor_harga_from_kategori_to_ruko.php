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
        // 1. Drop column 'harga' from 'kategori' table
        Schema::table('kategori', function (Blueprint $table) {
            if (Schema::hasColumn('kategori', 'harga')) {
                $table->dropColumn('harga');
            }
        });

        // 2. Add column 'harga' to 'ruko' table
        Schema::table('ruko', function (Blueprint $table) {
            if (!Schema::hasColumn('ruko', 'harga')) {
                $table->integer('harga')->after('kategori_id')->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Add column 'harga' back to 'kategori' table
        Schema::table('kategori', function (Blueprint $table) {
            if (!Schema::hasColumn('kategori', 'harga')) {
                $table->integer('harga')->after('nama')->default(0);
            }
        });

        // 2. Drop column 'harga' from 'ruko' table
        Schema::table('ruko', function (Blueprint $table) {
            if (Schema::hasColumn('ruko', 'harga')) {
                $table->dropColumn('harga');
            }
        });
    }
};
