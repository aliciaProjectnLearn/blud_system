<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  public function up()
{
    Schema::create('log_activities', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id')->nullable(); // jika user dihapus, log tetap ada? lebih baik nullable
        $table->string('nama_user')->nullable(); // bisa diisi nama user saat itu, agar tetap muncul meskipun user dihapus
        $table->string('sistem'); // misal: 'Futsal', 'AC', 'Ruko', 'Auth', dll
        $table->string('aktivitas'); // create, update, delete, login, logout, dll
        $table->text('deskripsi_aktivitas')->nullable();
        $table->timestamps();

        // optional: foreign key ke users, tapi jika user dihapus, log tetap ada
        $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_activities');
    }
};
