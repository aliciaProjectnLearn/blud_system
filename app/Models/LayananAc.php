<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LayananAc extends Model
{
    protected $table = 'layanan_ac';

    protected $fillable = [
        'kategori_id',
        'nama',
        'kapasitas_ac',
        'harga_jasa',
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function bookings()
    {
        return $this->hasMany(BookingAc::class, 'layanan_id');
    }
}
