<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $table = 'kategori';

    protected $fillable = ['nama', 'prefix', 'tipe'];

    public function rukos()
    {
        return $this->hasMany(Ruko::class);
    }

    public function layananAc()
    {
        return $this->hasMany(LayananAc::class, 'kategori_id');
    }
}
