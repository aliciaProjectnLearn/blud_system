<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

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
        'status_aktif'
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
        return DB::table('roles_users')
            ->join('roles', 'roles.id', '=', 'roles_users.role_id')
            ->where('roles_users.user_id', $this->id)
            ->where('roles.nama', $role)
            ->exists();
    }
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'user_id');
    }

    public function bookingFutsal()
    {
        return $this->hasManyThrough(BookingFutsal::class, Booking::class, 'user_id', 'booking_id');
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

    public function sewaRuko()
    {
        return $this->hasMany(\App\Models\SewaRuko::class, 'user_id');
    }

    public function bookingServis()
    {
        return $this->hasMany(BookingServis::class, 'user_id');
    }

    public function pembayaranServis()
    {
        return $this->hasManyThrough(PembayaranServis::class, BookingServis::class, 'user_id', 'booking_servis_id');
    }
    
    public function pekerjaanTeknisiServis()
    {
        return $this->hasMany(BookingServis::class, 'teknisi_id');
    }
}
