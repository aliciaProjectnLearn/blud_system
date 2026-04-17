<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    protected $fillable = [
        'jam_buka',
        'jam_tutup',
        'harga_reguler_futsal',
        'harga_event_futsal'
    ];
}
