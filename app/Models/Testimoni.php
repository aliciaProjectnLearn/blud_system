<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimoni extends Model
{
    protected $table = 'testimoni';

    protected $fillable = [
        'nama',
        'peran',
        'bintang',
        'isi',
        'tampil',
    ];

    protected $casts = [
        'tampil' => 'boolean',
    ];

    public function scopeTampil($query)
    {
        return $query->where('tampil', true);
    }
}
