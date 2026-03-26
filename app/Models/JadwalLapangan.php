<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalLapangan extends Model
{
    protected $table = 'jadwal_lapangan';

    protected $fillable = [
        'lapangan_id',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'status',
    ];

    public function lapangan()
    {
        return $this->belongsTo(Lapangan::class, 'lapangan_id');
    }
}
