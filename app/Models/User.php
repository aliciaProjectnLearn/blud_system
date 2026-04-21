<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Role;

/**
 * @property int $id
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'nama_lengkap',
        'no_hp',
        'nik',
        'email',
        'password',
        'role',
        'status_futsal',
        'alamat',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // ── Helper Methods ────────────────────────────────────

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'roles_users', 'user_id', 'role_id');
    }
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
    public function hasRole(string $role): bool
    {
        return $this->roles->contains('nama', $role);
    }
    public function bookingFutsal()
    {
        return $this->hasMany(BookingFutsal::class, 'user_id');
    }
    public function isPelangganReguler(): bool
    {
        return $this->bookingFutsal()
            ->where('jenis_pembayaran', 'reguler')
            ->exists();
    }

    public function bookingAc()
    {
        return $this->hasMany(BookingAc::class, 'user_id');
    }

    public function pekerjaanTeknisi()
    {
        return $this->hasMany(BookingAc::class, 'teknisi_id');
    }

    public function penyewa()
    {
        return $this->hasOne(Penyewa::class, 'user_id');
    }

    public function bookingServis()
    {
        return $this->hasMany(BookingServis::class, 'user_id');
    }

    public function pembayaranServis()
    {
        return $this->hasManyThrough(PembayaranServis::class, BookingServis::class, 'user_id', 'booking_servis_id');
    }
}
