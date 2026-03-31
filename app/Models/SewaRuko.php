<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SewaRuko extends Model
{
    protected $table = 'sewa_ruko';

    protected $fillable = [
        'booking_id',
        'penyewa_id',
        'ruko_id',
        'tgl_mulai',
        'tgl_selesai',
        'total_biaya_tahunan',
        'no_mou',
        'status',
    ];

    /**
     * Relasi ke model Penyewa.
     * Sekarang sewa_ruko.penyewa_id merujuk langsung ke penyewa.id
     */
    public function penyewa()
    {
        return $this->belongsTo(Penyewa::class, 'penyewa_id');
    }

    public function ruko()
    {
        return $this->belongsTo(Ruko::class);
    }

    public function pembayaran()
    {
        return $this->hasMany(PembayaranRuko::class, 'booking_id', 'booking_id');
    }

    public function dokumen()
{
    return $this->hasMany(DokumenPenyewaan::class, 'sewa_id');
}
}
