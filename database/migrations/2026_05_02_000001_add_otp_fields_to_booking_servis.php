<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booking_servis', function (Blueprint $table) {
            $table->string('otp_code', 6)->nullable()->after('access_token');
            $table->timestamp('otp_expires_at')->nullable()->after('otp_code');
            $table->boolean('otp_used')->default(false)->after('otp_expires_at');
            $table->integer('otp_attempt_count')->default(0)->after('otp_used');
            $table->timestamp('otp_blocked_until')->nullable()->after('otp_attempt_count');
            $table->timestamp('otp_sent_at')->nullable()->after('otp_blocked_until');
        });
    }

    public function down(): void
    {
        Schema::table('booking_servis', function (Blueprint $table) {
            $table->dropColumn([
                'otp_code',
                'otp_expires_at',
                'otp_used',
                'otp_attempt_count',
                'otp_blocked_until',
                'otp_sent_at',
            ]);
        });
    }
};
