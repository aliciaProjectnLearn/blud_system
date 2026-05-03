<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sewa_ruko', function (Blueprint $table) {
            $table->timestamp('token_expired_at')->nullable()->after('access_token');
        });
    }

    public function down(): void
    {
        Schema::table('sewa_ruko', function (Blueprint $table) {
            $table->dropColumn('token_expired_at');
        });
    }
};
