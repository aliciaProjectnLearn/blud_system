<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailServis extends Model
{
    protected $table = 'detail_servis';

    protected $fillable = [
        'booking_id',
        'item',
        'satuan',
        'quantity',
        'harga',
        'subtotal',
        'catatan',
    ];

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
            $model->subtotal = $model->quantity * $model->harga;
        });
    }
}
