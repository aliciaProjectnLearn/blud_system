<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpToken extends Model
{
    protected $fillable = [
        'access_token',
        'otp_code',
        'expires_at',
        'is_used',
        'attempt_count',
        'blocked_until',
        'sent_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'blocked_until' => 'datetime',
        'sent_at' => 'datetime',
        'is_used' => 'boolean',
    ];
}
