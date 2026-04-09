<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembatalan extends Model
{
    protected $table = 'pembatalan';

    protected $fillable = [
        'booking_id',
        'alasan',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }
}
