<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    // Audit tidak boleh diupdate
    public $timestamps = false;
    const CREATED_AT = 'created_at';

    protected $fillable = [
        'sistem',
        'tabel_entitas',
        'entitas_id',
        'aksi',
        'data_lama',
        'data_baru',
        'tipe_pelaku',
        'dilakukan_oleh',
        'nama_pelaku',
        'keterangan',
        'ip_address',
    ];

    protected $casts = [
        'data_lama'  => 'array',
        'data_baru'  => 'array',
        'created_at' => 'datetime',
    ];

    // Relasi ke admin
    public function admin()
    {
        return $this->belongsTo(User::class, 'dilakukan_oleh');
    }

    // Label aksi yang readable
    public function getLabelAksiAttribute(): string
    {
        $labels = [
            'booking_dibuat'        => '📋 Booking Dibuat',
            'status_diubah'         => '🔄 Status Diubah',
            'data_diupdate'         => '✏️ Data Diupdate',
            'dokumen_diupload'      => '📄 Dokumen Diupload',
            'mou_digenerate'        => '📝 MOU Digenerate',
            'pembayaran_diverifikasi' => '✅ Pembayaran Diverifikasi',
            'pembayaran_ditolak'    => '❌ Pembayaran Ditolak',
            'sewa_dibatalkan'       => '🚫 Sewa Dibatalkan',
            'sewa_disetujui'        => '✅ Sewa Disetujui',
            'sewa_ditolak'          => '❌ Sewa Ditolak',
            'sewa_diaktifkan'       => '🔓 Sewa Diaktifkan',
            'sewa_selesai'          => '🏁 Sewa Selesai',
        ];
        return $labels[$this->aksi] ?? ucfirst(str_replace('_', ' ', $this->aksi));
    }

    // Badge color untuk aksi
    public function getBadgeColorAttribute(): string
    {
        $colors = [
            'booking_dibuat'          => 'primary',
            'status_diubah'           => 'info',
            'sewa_disetujui'          => 'success',
            'sewa_ditolak'            => 'danger',
            'sewa_dibatalkan'         => 'danger',
            'sewa_diaktifkan'         => 'success',
            'sewa_selesai'            => 'secondary',
            'pembayaran_diverifikasi' => 'success',
            'pembayaran_ditolak'      => 'danger',
            'dokumen_diupload'        => 'warning',
            'mou_digenerate'          => 'warning',
            'data_diupdate'           => 'info',
        ];
        return $colors[$this->aksi] ?? 'secondary';
    }
}
