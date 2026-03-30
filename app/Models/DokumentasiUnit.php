<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DokumentasiUnit extends Model
{
    protected $table = 'dokumentasi_unit';

    protected $fillable = [
        'ruko_id',
        'file',
        'tipe',
        'judul_dokumen',
        'deskripsi',
    ];

    /**
     * Relasi ke model Ruko (Many-to-One)
     */
    public function ruko()
    {
        return $this->belongsTo(Ruko::class);
    }
}
