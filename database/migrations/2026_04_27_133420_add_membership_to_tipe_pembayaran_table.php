<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $exists = DB::table('tipe_pembayaran')->where('id', 4)->exists();
        if (!$exists) {
            DB::table('tipe_pembayaran')->insert([
                'id' => 4,
                'nama' => 'Membership',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    public function down(): void
    {
        DB::table('tipe_pembayaran')->where('id', 4)->delete();
    }
};
