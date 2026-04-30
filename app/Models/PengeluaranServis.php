<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengeluaranServis extends Model
{
    use HasFactory;

    protected $fillable = [
        'tanggal',
        'keterangan',
        'jumlah',
        'kategori',
        'created_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah' => 'decimal:2',
    ];

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
