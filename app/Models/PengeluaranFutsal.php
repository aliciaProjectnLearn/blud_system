<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengeluaranFutsal extends Model
{
    use HasFactory;

    protected $table = 'pengeluaran_futsals';

    protected $fillable = [
        'kode_pengeluaran',
        'tgl_pengeluaran',
        'nominal',
        'deskripsi',
        'kategori',
    ];
}
