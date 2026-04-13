<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RukoTableSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil ID kategori berdasarkan nama (sudah dibuat oleh KategoriTableSeeder)
        $idKantinBesar    = DB::table('kategori')->where('nama', 'Kantin Besar')->value('id');
        $idKantinContainer = DB::table('kategori')->where('nama', 'Kantin Container')->value('id');
        $idRukoDepan      = DB::table('kategori')->where('nama', 'Ruko Depan')->value('id');

        /**
         * Unit kantin: harga = Rp 9.000.000/tahun
         * Unit ruko  : harga = Rp 20.000.000/tahun
         *
         * Status bervariasi:
         *  - KNT001..KNT003 → terisi (data sewa akan dibuat di SewaRukoTableSeeder)
         *  - KNT004..KNT005 → kosong (tersedia untuk booking baru)
         *  - CNT001         → terisi
         *  - RKO001..RKO002 → terisi
         *  - RKO003         → kosong
         */
        $units = [
            // ── Kantin Besar ─────────────────────────────────────────
            [
                'kode_unit'   => 'KNT001',
                'kategori_id' => $idKantinBesar,
                'harga'       => 9000000,
                'status_unit' => 'terisi',
            ],
            [
                'kode_unit'   => 'KNT002',
                'kategori_id' => $idKantinBesar,
                'harga'       => 9000000,
                'status_unit' => 'kosong',
            ],
            [
                'kode_unit'   => 'KNT003',
                'kategori_id' => $idKantinBesar,
                'harga'       => 9000000,
                'status_unit' => 'kosong',
            ],
            [
                'kode_unit'   => 'KNT004',
                'kategori_id' => $idKantinBesar,
                'harga'       => 9000000,
                'status_unit' => 'kosong',
            ],
            [
                'kode_unit'   => 'KNT005',
                'kategori_id' => $idKantinBesar,
                'harga'       => 9000000,
                'status_unit' => 'kosong',
            ],
            [
                'kode_unit'   => 'KNT006',
                'kategori_id' => $idKantinBesar,
                'harga'       => 9000000,
                'status_unit' => 'kosong',
            ],
            [
                'kode_unit'   => 'KNT007',
                'kategori_id' => $idKantinBesar,
                'harga'       => 9000000,
                'status_unit' => 'kosong',
            ],
            [
                'kode_unit'   => 'KNT008',
                'kategori_id' => $idKantinBesar,
                'harga'       => 9000000,
                'status_unit' => 'kosong',
            ],
            [
                'kode_unit'   => 'KNT009',
                'kategori_id' => $idKantinBesar,
                'harga'       => 9000000,
                'status_unit' => 'kosong',
            ],

            // ── Kantin Container ──────────────────────────────────────
            [
                'kode_unit'   => 'CNT001',
                'kategori_id' => $idKantinContainer,
                'harga'       => 9000000,
                'status_unit' => 'terisi',
            ],
            [
                'kode_unit'   => 'CNT002',
                'kategori_id' => $idKantinContainer,
                'harga'       => 9000000,
                'status_unit' => 'kosong',
            ],
            [
                'kode_unit'   => 'CNT003',
                'kategori_id' => $idKantinContainer,
                'harga'       => 9000000,
                'status_unit' => 'kosong',
            ],
            [
                'kode_unit'   => 'CNT004',
                'kategori_id' => $idKantinContainer,
                'harga'       => 9000000,
                'status_unit' => 'kosong',
            ],
            [
                'kode_unit'   => 'CNT005',
                'kategori_id' => $idKantinContainer,
                'harga'       => 9000000,
                'status_unit' => 'kosong',
            ],

            // ── Ruko Depan ────────────────────────────────────────────
            [
                'kode_unit'   => 'RKO001',
                'kategori_id' => $idRukoDepan,
                'harga'       => 20000000,
                'status_unit' => 'terisi',
            ],
            [
                'kode_unit'   => 'RKO002',
                'kategori_id' => $idRukoDepan,
                'harga'       => 20000000,
                'status_unit' => 'kosong',
            ],
            [
                'kode_unit'   => 'RKO003',
                'kategori_id' => $idRukoDepan,
                'harga'       => 20000000,
                'status_unit' => 'kosong',
            ],
            [
                'kode_unit'   => 'RKO004',
                'kategori_id' => $idRukoDepan,
                'harga'       => 20000000,
                'status_unit' => 'kosong',
            ],
        ];

        foreach ($units as $u) {
            DB::table('ruko')->updateOrInsert(
                ['kode_unit' => $u['kode_unit']],
                array_merge($u, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        $this->command->info('RukoTableSeeder: ' . count($units) . ' unit berhasil di-seed.');
    }
}