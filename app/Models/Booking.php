<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $table = 'booking';

    protected $fillable = [
        'user_id',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bookingFutsal()
    {
        return $this->hasOne(BookingFutsal::class, 'booking_id');
    }

    public function pembayaranFutsal()
    {
        return $this->hasMany(PembayaranFutsal::class, 'booking_id');
    }

    public function bookingAc()
    {
        return $this->hasOne(BookingAc::class, 'booking_id');
    }

    public function pembatalan()
    {
        return $this->hasOne(Pembatalan::class, 'booking_id');
    }
}
