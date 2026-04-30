<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BookingFutsal extends Model
{
    protected $table = 'booking_futsal';

    protected $fillable = [
        'access_token',
        'booking_id',
        'lapangan_id',
        'nama_pemesan',
        'no_hp',
        'start_datetime',
        'end_datetime',
        'jenis_pembayaran',
        'status',
        'catatan',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime'   => 'datetime',
    ];

    public function getStartWithToleranceAttribute()
    {
        return $this->start_datetime->copy()->addMinutes(10)->format('H:i');
    }

    public function getDurasiJamAttribute()
    {
        return $this->start_datetime->diffInHours($this->end_datetime);
    }

    public function getDurasiHariAttribute()
    {
        return $this->start_datetime->diffInDays($this->end_datetime);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function lapangan()
    {
        return $this->belongsTo(Lapangan::class, 'lapangan_id');
    }

    public function pembayaranFutsal()
    {
        return $this->hasOne(PembayaranFutsal::class, 'booking_id');
    }

    public static function generateToken()
    {
        return Str::random(32);
    }

    public function scopeByNoHp($query, $noHp)
    {
        return $query->where('no_hp', $noHp);
    }
}
