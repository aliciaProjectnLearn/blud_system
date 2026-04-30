<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penyewa extends Model
{
    use HasFactory;

    protected $table = 'penyewa';

    protected $fillable = [
        'user_id',
        'nama_usaha',
        'alamat',
    ];

    /**
     * Relasi ke model User (1 Penyewa dimiliki 1 User)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke sewa_ruko.
     * Setelah migrasi terbaru, sewa_ruko.penyewa_id merujuk langsung ke penyewa.id
     */
    public function sewaRuko()
    {
        return $this->hasMany(SewaRuko::class, 'penyewa_id', 'id');
    }
}
