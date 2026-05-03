<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengeluaranAc extends Model
{
    use HasFactory;

    protected $table = 'pengeluaran_ac';
    protected $fillable = ['nominal', 'deskripsi', 'tanggal', 'kategori'];
}
