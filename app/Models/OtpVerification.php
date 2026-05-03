<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpVerification extends Model
{
    protected $fillable = [
        'token_sewa', 'no_hp', 'kode_otp', 
        'expired_at', 'sudah_digunakan'
    ];

    protected $casts = [
        'expired_at'      => 'datetime',
        'sudah_digunakan' => 'boolean',
    ];

    public function isValid(): bool
    {
        return !$this->sudah_digunakan && now()->isBefore($this->expired_at);
    }
}
