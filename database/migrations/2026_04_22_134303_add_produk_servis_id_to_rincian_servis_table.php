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
        Schema::table('rincian_servis', function (Blueprint $table) {
            $table->foreignId('produk_servis_id')->nullable()->after('kategori_komponen_id')->constrained('produk_servis')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rincian_servis', function (Blueprint $table) {
            $table->dropForeign(['produk_servis_id']);
            $table->dropColumn('produk_servis_id');
        });
    }
};
