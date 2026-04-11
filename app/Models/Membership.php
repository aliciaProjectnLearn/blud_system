<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Membership extends Model
{
    protected $table = 'membership';

    protected $fillable = [
        'user_id',
        'paket_membership_id',
        'transaksi_id',
        'total_kuota',
        'sisa_kuota',
        'tgl_daftar',
        'status',
    ];

    // Relasi
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function paket()
    {
        return $this->belongsTo(PaketMembership::class, 'paket_membership_id');
    }

    public function transaksi()
    {
        return $this->belongsTo(PembayaranFutsal::class, 'transaksi_id');
    }

    // Logic penggunaan kuota (dipanggil saat booking)
    public function gunakanKuota($durasi = 1): void
    {
        if ($this->status !== 'aktif') {
            throw new \Exception('Membership tidak aktif.');
        }

        if ($this->sisa_kuota < $durasi) {
            throw new \Exception('Kuota membership tidak mencukupi.');
        }

        DB::transaction(function () use ($durasi) {
            $this->decrement('sisa_kuota', $durasi);
            $this->refresh();

            if ($this->sisa_kuota === 0) {
                $this->update(['status' => 'tidak aktif']);
            }
        });
    }

    // Logic pengembalian kuota (dipanggil saat pembatalan booking membership)
    public function kembalikanKuota($durasi = 1): void
    {
        DB::transaction(function () use ($durasi) {
            if ($this->sisa_kuota < $this->total_kuota) {
                $this->increment('sisa_kuota', $durasi);
                $this->refresh();

                // Pastikan tidak melebihi total kuota
                if ($this->sisa_kuota > $this->total_kuota) {
                    $this->update(['sisa_kuota' => $this->total_kuota]);
                }

                // Aktifkan kembali jika sebelumnya 'tidak aktif' karena habis, tapi masih belum kadaluarsa (jika ada masa aktif)
                if ($this->status === 'tidak aktif' && $this->sisa_kuota > 0) {
                    $this->update(['status' => 'aktif']);
                }
            }
        });
    }
}
