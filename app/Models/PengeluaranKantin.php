<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengeluaranKantin extends Model
{
    use HasFactory;

    protected $table = 'pengeluaran_kantin';

    protected $fillable = [
        'nominal',
        'deskripsi',
        'tanggal',
        'kategori_pengeluaran',
    ];
}
