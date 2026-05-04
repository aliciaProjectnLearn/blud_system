<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModelKendaraan extends Model
{
    protected $table = 'model_kendaraan';

    protected $fillable = [
        'merek_kendaraan_id',
        'nama_model',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function merek()
    {
        return $this->belongsTo(MerekKendaraan::class, 'merek_kendaraan_id');
    }
}
