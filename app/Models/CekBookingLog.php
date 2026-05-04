<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CekBookingLog extends Model
{
    public $timestamps = false;
    
    protected $fillable = [
        'no_hp',
        'aksi',
        'ip_address',
        'user_agent',
    ];
}
