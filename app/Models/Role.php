<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['nama'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'roles_users', 'role_id', 'user_id');
    }

    /**
     * Menampilkan nama role dalam format yang mudah dibaca manusia.
     * Gunakan $role->label di semua view, bukan $role->nama.
     */
    public function getLabelAttribute(): string
    {
        // Map berbasis lowercase dari nilai 'nama' di database
        $map = [
            // Superadmin
            'superadmin'    => 'Super Admin',
            'Superadmin'    => 'Super Admin',

            // Admin per sistem
            'adminfutsal'   => 'Admin Futsal',
            'Adminfutsal'   => 'Admin Futsal',
            'adminkantin'   => 'Admin Kantin',
            'Adminkantin'   => 'Admin Kantin',
            'adminac'       => 'Admin AC',
            'Adminac'       => 'Admin AC',
            'adminservis'   => 'Admin Servis',
            'Adminservis'   => 'Admin Servis',

            // Kasir
            'kasirservis'   => 'Kasir Servis',
            'Kasirservis'   => 'Kasir Servis',
            'Kasir'         => 'Kasir',
            'kasir'         => 'Kasir',

            // Teknisi
            'teknisiac'     => 'Teknisi AC',
            'Teknisiac'     => 'Teknisi AC',
            'Teknisi'       => 'Teknisi AC',
            'teknisi'       => 'Teknisi AC',
            'teknisiservis' => 'Teknisi Servis',
            'Teknisiservis' => 'Teknisi Servis',
            'Teknisi Motor' => 'Teknisi Motor',
            'Teknisi Mobil' => 'Teknisi Mobil',

            // User biasa
            'user'          => 'User',
            'User'          => 'User',
            'pelanggan'     => 'Pelanggan',
            'Pelanggan'     => 'Pelanggan',
        ];

        return $map[$this->nama] ?? $this->nama;
    }
}
