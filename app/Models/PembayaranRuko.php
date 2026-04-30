<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranRuko extends Model
{
    protected $table = 'pembayaran_ruko';

    protected $fillable = [
        'sewa_ruko_id', 
        'termin_ke', 
        'jumlah_bayar', 
        'tipe_pembayaran', 
        'status_pembayaran', 
        'bukti_pembayaran', 
        'tanggal_bayar', 
        'catatan_admin',
        'booking_id', // legacy
        'tipe_pembayaran_id', // legacy
        'termin', // legacy
        'tgl_jatuh_tempo', 
        'jumlah_tagihan', 
        'status', // legacy
        'no_kwitansi',
    ];

    public function sewaRuko()
    {
        return $this->belongsTo(SewaRuko::class, 'sewa_ruko_id');
    }

    public function history()
    {
        return $this->hasMany(HistoryPembayaranRuko::class, 'pembayaran_ruko_id');
    }

    public function isTerlambat(): bool
    {
        return $this->status_pembayaran === 'pending'
            && $this->tgl_jatuh_tempo
            && now()->gt($this->tgl_jatuh_tempo);
    }

    public static function generateNoKwitansi()
    {
        $year = date('Y');
        $month = date('m');
        $prefix = "KWT/RU/$year/$month/";
        
        $last = self::where('no_kwitansi', 'like', $prefix . '%')
                    ->orderBy('id', 'desc')
                    ->first();
                    
        if (!$last) {
            $seq = 1;
        } else {
            $parts = explode('/', $last->no_kwitansi);
            $seq = (int) end($parts) + 1;
        }
        
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    public function tipe()
    {
        return $this->belongsTo(TipePembayaran::class, 'tipe_pembayaran_id');
    }
}
