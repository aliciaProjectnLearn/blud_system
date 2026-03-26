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
        'tgl_main',
        'jam_mulai',
        'jam_mulai_efektif',
        'jam_selesai',
        'durasi_main',
        'jenis_pembayaran',
    ];

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
