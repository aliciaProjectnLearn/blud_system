<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DokumenPenyewaan extends Model
{
    protected $table = 'tb_dokumen_penyewaan';

    protected $fillable = [
        'no_mou',
        'sewa_id',
        'nama_dokumen',
        'path_file'
    ];

    public function sewa()
    {
        return $this->belongsTo(SewaRuko::class, 'sewa_id');
    }
}