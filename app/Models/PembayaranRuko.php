<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranRuko extends Model
{
    protected $table = 'pembayaran_ruko';

    protected $fillable = [
        'booking_id', 'tipe_pembayaran_id', 'termin',
        'tgl_jatuh_tempo', 'jumlah_tagihan', 'tgl_bayar',
        'status', 'no_kwitansi',
    ];

    public function sewaRuko()
    {
        return $this->belongsTo(SewaRuko::class, 'booking_id', 'booking_id');
    }
}
