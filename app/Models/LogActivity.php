<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama_user',
        'sistem',
        'aktivitas',
        'deskripsi_aktivitas',
    ];

    // Relasi ke user (optional)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}