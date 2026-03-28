<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SewaRuko extends Model
{
    protected $table = 'sewa_ruko';

    protected $fillable = [
        'booking_id', 'penyewa_id', 'ruko_id',
        'tgl_mulai', 'tgl_selesai', 'total_biaya_tahunan',
        'no_mou', 'status',
    ];

    public function penyewa()
    {
        return $this->belongsTo(User::class, 'penyewa_id');
    }

    public function ruko()
    {
        return $this->belongsTo(Ruko::class);
    }

    public function pembayaran()
    {
        return $this->hasMany(PembayaranRuko::class, 'booking_id', 'booking_id');
    }
}
