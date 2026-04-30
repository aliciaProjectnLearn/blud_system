<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FotoServis extends Model
{
    use HasFactory;

    protected $table = 'foto_servis';

    protected $fillable = [
        'booking_servis_id',
        'path_foto',
        'keterangan',
    ];

    public function bookingServis()
    {
        return $this->belongsTo(BookingServis::class, 'booking_servis_id');
    }
}
