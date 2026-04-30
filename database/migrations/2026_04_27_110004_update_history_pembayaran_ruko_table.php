<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('history_pembayaran_ruko', function (Blueprint $table) {
            if (!Schema::hasColumn('history_pembayaran_ruko', 'pembayaran_ruko_id')) {
                $table->foreignId('pembayaran_ruko_id')->nullable()->after('id')->constrained('pembayaran_ruko')->onDelete('cascade');
            }
            if (!Schema::hasColumn('history_pembayaran_ruko', 'aksi')) {
                $table->string('aksi')->after('pembayaran_ruko_id');
            }
            if (!Schema::hasColumn('history_pembayaran_ruko', 'keterangan')) {
                $table->text('keterangan')->nullable()->after('aksi');
            }
            if (!Schema::hasColumn('history_pembayaran_ruko', 'dilakukan_oleh')) {
                $table->string('dilakukan_oleh')->nullable()->after('keterangan');
            }

            // Drop old column
            if (Schema::hasColumn('history_pembayaran_ruko', 'booking_id')) {
                // Check if foreign key exists before dropping
                try {
                    $table->dropForeign(['booking_id']);
                } catch (\Exception $e) {}
                $table->dropColumn('booking_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('history_pembayaran_ruko', function (Blueprint $table) {
            $table->dropForeign(['pembayaran_ruko_id']);
            $table->dropColumn(['pembayaran_ruko_id', 'aksi', 'keterangan', 'dilakukan_oleh']);
            $table->foreignId('booking_id')->nullable()->constrained('booking');
        });
    }
};
