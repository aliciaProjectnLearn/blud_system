<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoryPembayaranRuko extends Model
{
    protected $table = 'history_pembayaran_ruko';

    protected $fillable = [
        'pembayaran_ruko_id',
        'aksi',
        'keterangan',
        'dilakukan_oleh',
    ];

    public function pembayaran()
    {
        return $this->belongsTo(PembayaranRuko::class, 'pembayaran_ruko_id');
    }
}
