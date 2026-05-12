<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ruko extends Model
{
    protected $table = 'ruko';

    protected $fillable = [
        'kode_unit', 
        'kategori_id', 
        'harga', 
        'nama_ruko',
        'ukuran_ruko',
        'deskripsi',
        'foto_ruko',
        'posisi_x',
        'posisi_y',
        'status',
        'metode_pembayaran_unit',
        'status_unit', // legacy
    ];

    public function getStatusUnitAttribute()
    {
        return $this->status === 'tersedia' ? 'kosong' : 'terisi';
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    public function sewaRuko()
    {
        return $this->hasMany(SewaRuko::class);
    }

    public function dokumentasiUnit()
    {
        return $this->hasMany(DokumentasiUnit::class);
    }
}
