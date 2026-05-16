<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    protected $fillable = [
        'jam_buka',
        'jam_tutup',
        'harga_reguler_futsal',
        'harga_event_futsal',
        'jam_blokir_aktif',
        'jam_blokir_mulai',
        'jam_blokir_selesai',
        'hari_blokir',
        'keterangan_blokir',
    ];

    protected $casts = [
        'jam_blokir_aktif' => 'boolean',
    ];
}
