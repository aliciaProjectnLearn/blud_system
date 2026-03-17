<?php

namespace App\Traits;

use App\Models\LogActivity;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

trait Loggable
{
    /**
     * Catat aktivitas ke log.
     *
     * @param string $sistem Nama sistem/modul (misal 'Futsal', 'AC', 'Auth')
     * @param string $aktivitas Jenis aktivitas (create, update, delete, login, dll)
     * @param string|null $deskripsi Deskripsi detail
     * @param int|null $userId Jika null, pakai user yang sedang login
     * @return void
     */
    public static function function_log($sistem, $aktivitas, $deskripsi = null, $userId = null)
    {
        $user = null;
        if ($userId) {
            $user = User::find($userId);
        } else {
            $user = Auth::user();
        }

        LogActivity::create([
            'user_id' => $user->id ?? null,
            'nama_user' => $user->name ?? 'System',
            'sistem' => $sistem,
            'aktivitas' => $aktivitas,
            'deskripsi_aktivitas' => $deskripsi,
        ]);
    }
}