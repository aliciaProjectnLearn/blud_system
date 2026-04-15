<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * SewaRukoTableSeeder
 *
 * Membuat data sewa untuk unit yang berstatus 'terisi'.
 * Unit terisi: KNT001, KNT002, KNT003, CNT001, RKO001, RKO002
 *
 * Variasi status sewa:
 *  - KNT001 (pelanggan)  → aktif  : termin1 lunas, termin2 menunggu
 *  - KNT002 (pelanggan2) → pending : termin1 verifikasi
 *  - KNT003 (pelanggan3) → selesai : semua termin lunas, tgl_selesai sudah lewat
 *  - CNT001 (pelanggan)  → aktif  : termin1 & termin2 lunas
 *  - RKO001 (pelanggan4) → aktif  : termin1 lunas, termin2 menunggu
 *  - RKO002 (pelanggan2) → pending : termin1 menunggu
 */
class SewaRukoTableSeeder extends Seeder
{
    public function run(): void
    {
        // Bersihkan data lama agar idempoten
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('pembayaran_ruko')->truncate();
        DB::table('sewa_ruko')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Ambil id ruko berdasarkan kode_unit
        $ruko = DB::table('ruko')
            ->whereIn('kode_unit', ['KNT001', 'KNT002', 'KNT003', 'CNT001', 'RKO001', 'RKO002'])
            ->get()
            ->keyBy('kode_unit');

        // Ambil id user pelanggan
        $users = DB::table('users')
            ->whereIn('username', ['pelanggan', 'pelanggan2', 'pelanggan3', 'pelanggan4'])
            ->pluck('id', 'username');

        // Ambil id penyewa (relasi user_id → penyewa.id)
        $penyewa = DB::table('penyewa')
            ->whereIn('user_id', $users->values())
            ->get()
            ->keyBy('user_id');

        $getPenyewaId = function (string $username) use ($users, $penyewa): ?int {
            $uid = $users[$username] ?? null;
            return $uid ? ($penyewa[$uid]->id ?? null) : null;
        };

        $now = Carbon::now();

        /**
         * Konfigurasi sewa: kode_unit, username penyewa, tgl_mulai, status
         */
        $sewaConfig = [
            // KNT001 — Aktif, mulai 8 bulan lalu
            [
                'kode_unit'   => 'KNT001',
                'penyewa'     => 'pelanggan',
                'tgl_mulai'   => $now->copy()->subMonths(8),
                'status_sewa' => 'aktif',
            ],
            // KNT002 — Pending, mulai 3 hari lalu
            [
                'kode_unit'   => 'KNT002',
                'penyewa'     => 'pelanggan2',
                'tgl_mulai'   => $now->copy()->subDays(3),
                'status_sewa' => 'pending',
            ],
            // KNT003 — Selesai, mulai 15 bulan lalu (tgl_selesai sudah lewat)
            [
                'kode_unit'   => 'KNT003',
                'penyewa'     => 'pelanggan3',
                'tgl_mulai'   => $now->copy()->subMonths(15),
                'status_sewa' => 'selesai',
            ],
            // CNT001 — Aktif, mulai 5 bulan lalu, kedua termin lunas
            [
                'kode_unit'   => 'CNT001',
                'penyewa'     => 'pelanggan',
                'tgl_mulai'   => $now->copy()->subMonths(5),
                'status_sewa' => 'aktif',
            ],
            // RKO001 — Aktif, mulai 4 bulan lalu
            [
                'kode_unit'   => 'RKO001',
                'penyewa'     => 'pelanggan4',
                'tgl_mulai'   => $now->copy()->subMonths(4),
                'status_sewa' => 'aktif',
            ],
            // RKO002 — Pending, mulai 1 hari lalu
            [
                'kode_unit'   => 'RKO002',
                'penyewa'     => 'pelanggan2',
                'tgl_mulai'   => $now->copy()->subDay(),
                'status_sewa' => 'pending',
            ],
        ];

        foreach ($sewaConfig as $cfg) {
            $rukoRow = $ruko[$cfg['kode_unit']] ?? null;
            if (!$rukoRow) {
                $this->command->warn("  [skip] ruko '{$cfg['kode_unit']}' tidak ditemukan.");
                continue;
            }

            $penyewaId = $getPenyewaId($cfg['penyewa']);
            if (!$penyewaId) {
                $this->command->warn("  [skip] penyewa '{$cfg['penyewa']}' tidak ditemukan.");
                continue;
            }

            $tglMulai   = $cfg['tgl_mulai'];
            $tglSelesai = $tglMulai->copy()->addYear();
            $harga      = (int) $rukoRow->harga;
            $userId     = $users[$cfg['penyewa']];

            // Buat booking parent
            $bookingId = DB::table('booking')->insertGetId([
                'user_id'    => $userId,
                'status'     => 'selesai',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Buat sewa_ruko
            DB::table('sewa_ruko')->insert([
                'booking_id'         => $bookingId,
                'penyewa_id'         => $penyewaId,
                'ruko_id'            => $rukoRow->id,
                'tgl_mulai'          => $tglMulai->format('Y-m-d'),
                'tgl_selesai'        => $tglSelesai->format('Y-m-d'),
                'harga_sewa_tahunan' => $harga,
                'status'             => $cfg['status_sewa'],
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);

            $this->command->line("  → Sewa {$cfg['kode_unit']} [{$cfg['status_sewa']}] Rp " . number_format($harga, 0, ',', '.') . ' OK');
        }

        $this->command->info('SewaRukoTableSeeder: ' . count($sewaConfig) . ' sewa berhasil di-seed.');
    }
}