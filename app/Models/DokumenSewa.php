<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DokumenSewa extends Model
{
    protected $fillable = [
        'sewa_ruko_id',
        'tipe_dokumen',
        'nama_dokumen',
        'path_file',
        'diunggah_oleh',
        'keterangan'
    ];

    public function sewaRuko()
    {
        return $this->belongsTo(SewaRuko::class, 'sewa_ruko_id');
    }
}
