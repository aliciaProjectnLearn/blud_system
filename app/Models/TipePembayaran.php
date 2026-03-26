<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipePembayaran extends Model
{
    protected $table = 'tipe_pembayaran';

    protected $fillable = [
        'nama',
    ];
}
