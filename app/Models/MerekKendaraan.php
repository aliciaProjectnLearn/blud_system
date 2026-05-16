<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MerekKendaraan extends Model
{
    protected $table = 'merek_kendaraan';

    protected $fillable = [
        'nama',
        'tipe',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function modelKendaraan()
    {
        return $this->hasMany(ModelKendaraan::class, 'merek_kendaraan_id');
    }
}
