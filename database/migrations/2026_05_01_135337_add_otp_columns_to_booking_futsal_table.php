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
        Schema::table('booking_futsal', function (Blueprint $table) {
            $table->string('otp_code', 6)->nullable()->after('access_token');
            $table->timestamp('otp_expired_at')->nullable()->after('otp_code');
            $table->tinyInteger('otp_attempt')->default(0)->after('otp_expired_at');
            $table->timestamp('otp_blocked_until')->nullable()->after('otp_attempt');
            $table->timestamp('otp_sent_at')->nullable()->after('otp_blocked_until');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_futsal', function (Blueprint $table) {
            $table->dropColumn(['otp_code', 'otp_expired_at', 'otp_attempt', 'otp_blocked_until', 'otp_sent_at']);
        });
    }
};
