<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JamOperasionalLapangan extends Model
{
    protected $table = 'jam_operasional_lapangan';

    protected $fillable = [
        'lapangan_id',
        'hari',
        'jam_buka',
        'jam_tutup',
        'is_aktif',
    ];

    protected $casts = [
        'is_aktif' => 'boolean',
    ];

    public function lapangan()
    {
        return $this->belongsTo(Lapangan::class, 'lapangan_id');
    }

    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true);
    }
}
