<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaketMembership extends Model
{
    protected $table = 'paket_membership';

    protected $fillable = [
        'nama_paket',
        'jumlah_kuota',
        'harga',
        'status',
    ];

    public function memberships()
    {
        return $this->hasMany(Membership::class, 'paket_membership_id');
    }

    // Hitung total pengguna aktif paket ini
    public function totalPengguna(): int
    {
        return $this->memberships()->count();
    }

    public function totalPenggunaAktif(): int
    {
        return $this->memberships()->where('status', 'aktif')->count();
    }
}
