<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailServis extends Model
{
    protected $table = 'detail_servis';

    protected $fillable = [
        'booking_id',
        'tipe',
        'layanan_id',
        'produk_id',
        'item',
        'satuan',
        'quantity',
        'harga',
        'subtotal',
        'catatan',
    ];

    public function layanan()
    {
        return $this->belongsTo(LayananAc::class, 'layanan_id');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    public function bookingAc()
    {
        return $this->belongsTo(BookingAc::class, 'booking_id');
    }

    /**
     * Boot function to auto-calculate subtotal
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // Kalkulasi otomatis subtotal jika harga dan quantity ada
            if ($model->quantity && $model->harga) {
                $model->subtotal = $model->quantity * $model->harga;
            }
        });
    }
}