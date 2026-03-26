<?php

namespace App\Services;

use App\Models\Membership;
use App\Models\PaketMembership;
use Illuminate\Support\Facades\DB;

class MembershipService
{
    /**
     * Buat membership baru dari pembelian.
     * Hanya bisa dipanggil setelah pembayaran dikonfirmasi.
     *
     * @param int $userId
     * @param int $paketId
     * @param int $transaksiId  — ID dari pembayaran_futsal
     */
    public function buat(int $userId, int $paketId, int $transaksiId): Membership
    {
        // Ambil paket & validasi
        $paket = PaketMembership::findOrFail($paketId);

        if ($paket->status !== 'aktif') {
            throw new \Exception('Paket membership tidak tersedia.');
        }

        // Cegah duplikat membership dari transaksi yang sama
        $sudahAda = Membership::where('transaksi_id', $transaksiId)->exists();
        if ($sudahAda) {
            throw new \Exception('Membership untuk transaksi ini sudah dibuat.');
        }

        return DB::transaction(function () use ($userId, $paket, $transaksiId) {
            return Membership::create([
                'user_id'             => $userId,
                'paket_membership_id' => $paket->id,
                'transaksi_id'        => $transaksiId,
                'total_kuota'         => $paket->jumlah_kuota,  // ambil dari paket
                'sisa_kuota'          => $paket->jumlah_kuota,  // set sama saat awal
                'status'              => 'aktif',
            ]);
        });
    }
}
