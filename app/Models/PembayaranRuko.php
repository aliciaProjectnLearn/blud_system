<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranRuko extends Model
{
    protected $table = 'pembayaran_ruko';

    protected $fillable = [
        'booking_id', 'tipe_pembayaran_id', 'termin',
        'tgl_jatuh_tempo', 'jumlah_tagihan', 'tgl_bayar',
        'status', 'no_kwitansi', 'path_kwitansi',
    ];

    public function sewaRuko()
    {
        return $this->hasOne(SewaRuko::class, 'booking_id', 'booking_id');
    }

    public function booking()
    {
        return $this->sewaRuko();
    }

    public function tipe()
    {
        return $this->belongsTo(TipePembayaran::class, 'tipe_pembayaran_id');
    }

    // Cek apakah sudah jatuh tempo
    public function isTerlambat(): bool
    {
        return $this->status === 'menunggu'
            && $this->tgl_jatuh_tempo
            && now()->gt($this->tgl_jatuh_tempo);
    }

    // Generate no kwitansi otomatis
    public static function generateNoKwitansi(): string
    {
        $tahun  = now()->format('Y');
        $bulan  = now()->format('m');
        $latest = self::whereNotNull('no_kwitansi')
            ->whereYear('tgl_bayar', $tahun)
            ->whereMonth('tgl_bayar', $bulan)
            ->count();

        $urut = str_pad($latest + 1, 4, '0', STR_PAD_LEFT);
        return "KWT/{$tahun}/{$bulan}/{$urut}";
    }
}
