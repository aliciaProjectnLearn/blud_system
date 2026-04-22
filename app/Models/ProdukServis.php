<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdukServis extends Model
{
    use HasFactory;

    protected $table = 'produk_servis';

    protected $fillable = [
        'nama_produk',
        'tipe_kendaraan',
        'harga',
        'stok',
        'deskripsi',
        'kode_part',
        'merk',
        'satuan',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'stok' => 'integer',
    ];

    public function rincianServis()
    {
        return $this->hasMany(RincianServis::class, 'produk_servis_id');
    }
}
