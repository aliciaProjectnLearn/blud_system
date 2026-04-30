<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Adjust no_hp
            $table->string('no_hp')->nullable(false)->change();
            
            // Add unique index safely - using try-catch to avoid error if exists
            try {
                $table->unique('no_hp');
            } catch (\Exception $e) {
                // Already exists or other error
            }
            
            // Adjust nik
            $table->string('nik', 16)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('no_hp')->nullable()->dropUnique(['no_hp'])->change();
            $table->string('nik', 20)->nullable()->change();
        });
    }
};
