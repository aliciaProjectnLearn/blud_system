<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SewaRuko extends Model
{
    protected $table = 'sewa_ruko';

    protected $fillable = [
        'user_id',
        'ruko_id',
        'access_token',
        'nama_penyewa',
        'no_hp_snapshot',
        'nik_penyewa',
        'status_sewa',
        'tanggal_mulai_sewa',
        'tanggal_selesai_sewa',
        'tipe_pembayaran',
        'harga_sewa_tahunan',
        'catatan',
        'foto_ktp',
        'booking_id', // legacy
        'penyewa_id',
        'token_expired_at',
    ];

    public function ruko()
    {
        return $this->belongsTo(Ruko::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pembayaran()
    {
        return $this->hasMany(PembayaranRuko::class, 'sewa_ruko_id');
    }

    public function dokumen()
    {
        return $this->hasMany(\App\Models\DokumenSewa::class, 'sewa_ruko_id');
    }

    /**
     * Helper to find by token.
     */
    public static function findByToken($token)
    {
        return static::where('access_token', $token)->firstOrFail();
    }
}
