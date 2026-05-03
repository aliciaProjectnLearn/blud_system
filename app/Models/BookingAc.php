<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingAc extends Model
{
    protected $table = 'booking_ac';

    protected $fillable = [
        'booking_id',
        'user_id',
        'nama_pelanggan',
        'no_hp',
        'teknisi_id',
        'layanan_id',
        'tgl_kunjungan',
        'alamat',
        'merek_ac',
        'detail_keluhan',
        'status',
        'foto_hasil',
        'access_token',
    ];

    public function penggajian()
    {
        return $this->hasOne(PenggajianTeknisi::class, 'booking_ac_id');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function layanan()
    {
        return $this->belongsTo(LayananAc::class, 'layanan_id');
    }

    public function teknisi()
    {
        return $this->belongsTo(User::class, 'teknisi_id');
    }

    public function detailServis()
    {
        return $this->hasMany(DetailServis::class, 'booking_id');
    }

    public function pembayaran()
    {
        return $this->hasOne(PembayaranAc::class, 'booking_id', 'id');
    }
}
