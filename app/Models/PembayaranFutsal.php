<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class PembayaranFutsal extends Model
{
    // Konstanta Status Transaksi
    public const STATUS_MENUNGGU = 'menunggu';
    public const STATUS_VERIFIKASI = 'verifikasi';
    public const STATUS_DIBATALKAN = 'dibatalkan';

    protected $table = 'pembayaran_futsal';

    protected $fillable = [
        'kode_pembayaran',
        'jenis_transaksi',
        'booking_id',
        'tipe_pembayaran_id',
        'jumlah_bayar',
        'status',
        'bukti',
        'tgl_bayar',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $today = Carbon::now();
            $dateString = $today->format('Ymd');
            
            $lastTransaksi = self::where('kode_pembayaran', 'like', 'FSL-' . $dateString . '-%')
                ->orderBy('id', 'desc')
                ->first();

            $nextUrut = 1;
            if ($lastTransaksi && $lastTransaksi->kode_pembayaran) {
                $parts = explode('-', $lastTransaksi->kode_pembayaran);
                if (count($parts) === 3) {
                    $lastUrut = (int) $parts[2];
                    $nextUrut = $lastUrut + 1;
                }
            }

            $model->kode_pembayaran = 'FSL-' . $dateString . '-' . str_pad($nextUrut, 3, '0', STR_PAD_LEFT);
        });
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function tipePembayaran()
    {
        return $this->belongsTo(TipePembayaran::class, 'tipe_pembayaran_id');
    }

    public function membership()
    {
        return $this->hasOne(Membership::class, 'transaksi_id');
    }
}
