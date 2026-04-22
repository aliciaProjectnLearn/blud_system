<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LayananServis extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_layanan',
        'deskripsi',
        'harga_estimasi',
        'tipe_kendaraan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'harga_estimasi' => 'decimal:2',
    ];

    public function bookingServis()
    {
        return $this->hasMany(BookingServis::class, 'layanan_servis_id');
    }

    public function rincianServis()
    {
        return $this->hasManyThrough(RincianServis::class, BookingServis::class, 'layanan_servis_id', 'booking_servis_id');
    }
}
