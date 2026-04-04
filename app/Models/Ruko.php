<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ruko extends Model
{
    protected $table = 'ruko';

    protected $fillable = ['kode_unit', 'kategori_id', 'harga', 'status_unit'];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    public function sewaRuko()
    {
        return $this->hasMany(SewaRuko::class);
    }

    public function dokumentasiUnit()
    {
        return $this->hasMany(DokumentasiUnit::class);
    }
}
