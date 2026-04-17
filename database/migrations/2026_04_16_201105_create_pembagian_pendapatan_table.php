<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembagian_pendapatan', function (Blueprint $table) {
            $table->id();
            $table->enum('sistem', ['ac', 'kantin', 'futsal']);
            $table->string('penerima'); // jurusan, aplikasi, blud, bersih
            $table->decimal('persentase', 5, 2); // misal: 40.00
            $table->timestamps();

            $table->unique(['sistem', 'penerima']);
        });

        // Seed default
        DB::table('pembagian_pendapatan')->insert([
            // AC: jurusan 40%, aplikasi 10%, blud 40%, bersih 10%
            ['sistem' => 'ac', 'penerima' => 'jurusan',  'persentase' => 40.00, 'created_at' => now(), 'updated_at' => now()],
            ['sistem' => 'ac', 'penerima' => 'aplikasi', 'persentase' => 10.00, 'created_at' => now(), 'updated_at' => now()],
            ['sistem' => 'ac', 'penerima' => 'blud',     'persentase' => 40.00, 'created_at' => now(), 'updated_at' => now()],
            ['sistem' => 'ac', 'penerima' => 'bersih',   'persentase' => 10.00, 'created_at' => now(), 'updated_at' => now()],

            // Kantin: aplikasi 10%, blud 50%, bersih 40%
            ['sistem' => 'kantin', 'penerima' => 'aplikasi', 'persentase' => 10.00, 'created_at' => now(), 'updated_at' => now()],
            ['sistem' => 'kantin', 'penerima' => 'blud',     'persentase' => 50.00, 'created_at' => now(), 'updated_at' => now()],
            ['sistem' => 'kantin', 'penerima' => 'bersih',   'persentase' => 40.00, 'created_at' => now(), 'updated_at' => now()],

            // Futsal: aplikasi 10%, blud 50%, bersih 40%
            ['sistem' => 'futsal', 'penerima' => 'aplikasi', 'persentase' => 10.00, 'created_at' => now(), 'updated_at' => now()],
            ['sistem' => 'futsal', 'penerima' => 'blud',     'persentase' => 50.00, 'created_at' => now(), 'updated_at' => now()],
            ['sistem' => 'futsal', 'penerima' => 'bersih',   'persentase' => 40.00, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('pembagian_pendapatan');
    }
};
