<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranFutsal extends Model
{
    protected $table = 'pembayaran_futsal';

    protected $fillable = [
        'booking_id',
        'tipe_pembayaran_id',
        'jumlah_bayar',
        'status',
        'bukti',
        'tgl_bayar',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function tipePembayaran()
    {
        return $this->belongsTo(TipePembayaran::class, 'tipe_pembayaran_id');
    }
}
