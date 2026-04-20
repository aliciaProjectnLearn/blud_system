<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RincianServis extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_servis_id',
        'kategori_komponen_id',
        'nama_item',
        'jumlah',
        'harga_satuan',
        'subtotal',
        'keterangan',
    ];

    protected $casts = [
        'jumlah' => 'integer',
        'harga_satuan' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function bookingServis()
    {
        return $this->belongsTo(BookingServis::class, 'booking_servis_id');
    }

    public function kategoriKomponen()
    {
        return $this->belongsTo(KategoriKomponen::class, 'kategori_komponen_id');
    }
}
