<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BookingServis extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_booking',
        'user_id',
        'layanan_servis_id',
        'tipe_kendaraan',
        'merek_kendaraan',
        'nomor_plat',
        'tahun_kendaraan',
        'keluhan',
        'tanggal_booking',
        'jam_booking',
        'status',
        'catatan_admin',
        'teknisi_id',
        'access_token',
        'nama_pemesan',
        'no_hp',
        // OTP fields
        'otp_code',
        'otp_expires_at',
        'otp_used',
        'otp_attempt_count',
        'otp_blocked_until',
        'otp_sent_at',
    ];

    protected $casts = [
        'tanggal_booking'   => 'date',
        'otp_expires_at'    => 'datetime',
        'otp_blocked_until' => 'datetime',
        'otp_sent_at'       => 'datetime',
        'otp_used'          => 'boolean',
        'otp_attempt_count' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->kode_booking)) {
                $date = now()->format('Ymd');
                $random = strtoupper(Str::random(4));
                $model->kode_booking = "BS-{$date}-{$random}";
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pelanggan()
    {
        return $this->user();
    }

    public function layananServis()
    {
        return $this->belongsTo(LayananServis::class, 'layanan_servis_id');
    }

    public function teknisi()
    {
        return $this->belongsTo(User::class, 'teknisi_id');
    }

    public function rincianServis()
    {
        return $this->hasMany(RincianServis::class, 'booking_servis_id');
    }

    public function rincian()
    {
        return $this->rincianServis();
    }

    public function pembayaranServis()
    {
        return $this->hasOne(PembayaranServis::class, 'booking_servis_id');
    }

    public function fotoServis()
    {
        return $this->hasMany(FotoServis::class, 'booking_servis_id');
    }

    public function penggajianTeknisi()
    {
        return $this->hasOne(PenggajianTeknisi::class, 'booking_servis_id');
    }

    /**
     * Check if a booking slot is available.
     * Max 3 bookings per hour per day.
     */
    public static function isSlotAvailable($tanggal, $jam)
    {
        $count = self::where('tanggal_booking', $tanggal)
            ->where('jam_booking', $jam)
            ->whereNotIn('status', ['batal', 'selesai'])
            ->count();

        return $count < 3;
    }
}
