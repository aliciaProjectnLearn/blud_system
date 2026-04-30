<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenggajianTeknisi extends Model
{
    use HasFactory;

    protected $table = 'penggajian_teknisi';

    protected $fillable = [
        'teknisi_id',
        'booking_servis_id',
        'nominal',
        'status_bayar',
        'tanggal_bayar'
    ];

    public function teknisi()
    {
        return $this->belongsTo(User::class, 'teknisi_id');
    }

    public function bookingServis()
    {
        return $this->belongsTo(BookingServis::class, 'booking_servis_id');
    }
}
