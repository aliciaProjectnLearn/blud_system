<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembayaranServis extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_servis_id',
        'kode_pembayaran',
        'total_biaya',
        'tipe_pembayaran',
        'status_pembayaran',
        'jumlah_dp',
        'tanggal_bayar',
        'path_bukti_bayar',
        'catatan',
    ];

    protected $casts = [
        'total_biaya' => 'decimal:2',
        'jumlah_dp' => 'decimal:2',
        'tanggal_bayar' => 'datetime',
    ];

    public function bookingServis()
    {
        return $this->belongsTo(BookingServis::class, 'booking_servis_id');
    }
}
