<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Lapangan extends Model
{
    protected $table = 'lapangan';

    protected $fillable = [
        'nama',
        'ukuran',
        'deskripsi',
        'spesifikasi',
        'lokasi',
        'foto',
    ];

    public function jamOperasional()
    {
        return $this->hasMany(JamOperasionalLapangan::class);
    }

    public function getJamHariIni()
    {
        $hariIni = ucfirst(Carbon::now()->locale('id')->dayName);
        return $this->jamOperasional()
            ->where('hari', $hariIni)
            ->where('is_aktif', true)
            ->first();
    }
}
