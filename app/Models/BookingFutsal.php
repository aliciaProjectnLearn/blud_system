<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingFutsal extends Model
{
    protected $table = 'booking_futsal';

    protected $fillable = [
        'booking_id',
        'user_id',
        'lapangan_id',
        'start_datetime',
        'end_datetime',
        'type',
        'jenis_pembayaran',
        'access_token',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime'   => 'datetime',
    ];

    // Tambah accessor untuk toleransi 10 menit (display only)
    public function getStartWithToleranceAttribute()
    {
        return $this->start_datetime->copy()->addMinutes(10)->format('H:i');
    }

    // Hitung durasi dalam jam
    public function getDurasiJamAttribute()
    {
        return $this->start_datetime->diffInHours($this->end_datetime);
    }

    // Hitung durasi dalam hari
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
}
