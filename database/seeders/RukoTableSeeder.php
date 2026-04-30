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
                'nama_ruko'   => 'Kantin Utama 01',
                'kategori_id' => $idKantinBesar,
                'harga'       => 9000000,
                'ukuran_ruko' => '3 x 4 Meter (12m²)',
                'deskripsi'   => 'Unit kantin premium di area pintu masuk utama. Posisi sangat strategis dengan aliran pengunjung tinggi, cocok untuk menu makanan berat atau cafe.',
                'status_unit' => 'terisi',
                'status'      => 'disewa',
            ],
            [
                'kode_unit'   => 'KNT002',
                'nama_ruko'   => 'Kantin Utama 02',
                'kategori_id' => $idKantinBesar,
                'harga'       => 9000000,
                'ukuran_ruko' => '3 x 4 Meter (12m²)',
                'deskripsi'   => 'Unit kantin area tengah dengan akses air bersih dan wastafel terintegrasi. Dekat dengan area meja makan komunal.',
                'status_unit' => 'kosong',
                'status'      => 'tersedia',
            ],
            [
                'kode_unit'   => 'KNT003',
                'nama_ruko'   => 'Kantin Utama 03',
                'kategori_id' => $idKantinBesar,
                'harga'       => 9000000,
                'ukuran_ruko' => '3 x 4 Meter (12m²)',
                'deskripsi'   => 'Unit kantin siap pakai, sudah termasuk instalasi listrik standar dan pencahayaan yang cukup. Area bersih dan terawat.',
                'status_unit' => 'kosong',
                'status'      => 'tersedia',
            ],
            [
                'kode_unit'   => 'KNT004',
                'nama_ruko'   => 'Kantin Utama 04',
                'kategori_id' => $idKantinBesar,
                'harga'       => 9000000,
                'ukuran_ruko' => '3 x 4 Meter (12m²)',
                'deskripsi'   => 'Unit kantin area tengah, sangat cocok untuk usaha minuman kekinian atau snack ringan karena posisi terbuka di dua sisi.',
                'status_unit' => 'kosong',
                'status'      => 'tersedia',
            ],
            [
                'kode_unit'   => 'KNT005',
                'nama_ruko'   => 'Kantin Utama 05',
                'kategori_id' => $idKantinBesar,
                'harga'       => 9000000,
                'ukuran_ruko' => '3 x 4 Meter (12m²)',
                'deskripsi'   => 'Unit kantin standar di barisan tengah. Memiliki ventilasi udara yang sangat baik dan area penyimpanan yang cukup luas.',
                'status_unit' => 'kosong',
                'status'      => 'tersedia',
            ],
            [
                'kode_unit'   => 'KNT006',
                'nama_ruko'   => 'Kantin Utama 06',
                'kategori_id' => $idKantinBesar,
                'harga'       => 9000000,
                'ukuran_ruko' => '3 x 4 Meter (12m²)',
                'deskripsi'   => 'Unit kantin pojok dengan ruang tambahan di sisi samping. Sangat fleksibel untuk penataan rak display atau etalase.',
                'status_unit' => 'kosong',
                'status'      => 'tersedia',
            ],
            [
                'kode_unit'   => 'KNT007',
                'nama_ruko'   => 'Kantin Utama 07',
                'kategori_id' => $idKantinBesar,
                'harga'       => 9000000,
                'ukuran_ruko' => '3 x 4 Meter (12m²)',
                'deskripsi'   => 'Unit kantin area belakang dekat akses masuk suplai bahan. Tenang dan cocok untuk dapur produksi makanan yang butuh fokus.',
                'status_unit' => 'kosong',
                'status'      => 'tersedia',
            ],
            [
                'kode_unit'   => 'KNT008',
                'nama_ruko'   => 'Kantin Utama 08',
                'kategori_id' => $idKantinBesar,
                'harga'       => 9000000,
                'ukuran_ruko' => '3 x 4 Meter (12m²)',
                'deskripsi'   => 'Unit kantin standar area belakang. Memiliki akses langsung ke area cuci piring bersama dan sirkulasi udara optimal.',
                'status_unit' => 'kosong',
                'status'      => 'tersedia',
            ],
            [
                'kode_unit'   => 'KNT009',
                'nama_ruko'   => 'Kantin Utama 09',
                'kategori_id' => $idKantinBesar,
                'harga'       => 9000000,
                'ukuran_ruko' => '3 x 4 Meter (12m²)',
                'deskripsi'   => 'Unit kantin barisan belakang paling pojok. Harga kompetitif dengan fasilitas yang sama lengkapnya dengan unit lain.',
                'status_unit' => 'kosong',
                'status'      => 'tersedia',
            ],

            // ── Kantin Container ──────────────────────────────────────
            [
                'kode_unit'   => 'CNT001',
                'nama_ruko'   => 'Container A1',
                'kategori_id' => $idKantinContainer,
                'harga'       => 9000000,
                'ukuran_ruko' => '2.5 x 3 Meter',
                'deskripsi'   => 'Unit container estetik dengan desain industrial modern. Sangat populer untuk target market siswa milenial/gen-z.',
                'status_unit' => 'terisi',
                'status'      => 'disewa',
            ],
            [
                'kode_unit'   => 'CNT002',
                'nama_ruko'   => 'Container A2',
                'kategori_id' => $idKantinContainer,
                'harga'       => 9000000,
                'ukuran_ruko' => '2.5 x 3 Meter',
                'deskripsi'   => 'Unit container area taman. Suasana asri dan nyaman untuk area nongkrong outdoor di sore hari.',
                'status_unit' => 'kosong',
                'status'      => 'tersedia',
            ],
            [
                'kode_unit'   => 'CNT003',
                'nama_ruko'   => 'Container B1',
                'kategori_id' => $idKantinContainer,
                'harga'       => 9000000,
                'ukuran_ruko' => '2.5 x 3 Meter',
                'deskripsi'   => 'Unit container standar siap huni. Sudah dilengkapi dengan meja bar lipat di bagian depan.',
                'status_unit' => 'kosong',
                'status'      => 'tersedia',
            ],
            [
                'kode_unit'   => 'CNT004',
                'nama_ruko'   => 'Container B2',
                'kategori_id' => $idKantinContainer,
                'harga'       => 9000000,
                'ukuran_ruko' => '2.5 x 3 Meter',
                'deskripsi'   => 'Unit container strategis dekat jalur masuk parkir. Visibilitas sangat tinggi bagi siapa saja yang baru datang.',
                'status_unit' => 'kosong',
                'status'      => 'tersedia',
            ],

            // ── Ruko Depan ────────────────────────────────────────────
            [
                'kode_unit'   => 'RKO001',
                'nama_ruko'   => 'Ruko Bisnis 01',
                'kategori_id' => $idRukoDepan,
                'harga'       => 20000000,
                'ukuran_ruko' => '4 x 6 Meter (24m²)',
                'deskripsi'   => 'Ruko premium baris depan menghadap jalan utama. Memiliki facade kaca penuh, sangat cocok untuk minimarket atau toko retail besar.',
                'status_unit' => 'terisi',
                'status'      => 'disewa',
            ],
            [
                'kode_unit'   => 'RKO002',
                'nama_ruko'   => 'Ruko Bisnis 02',
                'kategori_id' => $idRukoDepan,
                'harga'       => 20000000,
                'ukuran_ruko' => '4 x 6 Meter (24m²)',
                'deskripsi'   => 'Ruko strategis di jalur protokol sekolah. Cocok untuk usaha jasa seperti Laundry, Fotocopy, atau Barber Shop.',
                'status_unit' => 'terisi',
                'status'      => 'disewa',
            ],
            [
                'kode_unit'   => 'RKO003',
                'nama_ruko'   => 'Ruko Bisnis 03',
                'kategori_id' => $idRukoDepan,
                'harga'       => 20000000,
                'ukuran_ruko' => '4 x 6 Meter (24m²)',
                'deskripsi'   => 'Ruko luas dengan langit-langit tinggi. Pencahayaan alami sangat baik, menghemat penggunaan listrik di siang hari.',
                'status_unit' => 'kosong',
                'status'      => 'tersedia',
            ],
            [
                'kode_unit'   => 'RKO004',
                'nama_ruko'   => 'Ruko Bisnis 04',
                'kategori_id' => $idRukoDepan,
                'harga'       => 20000000,
                'ukuran_ruko' => '4 x 6 Meter (24m²)',
                'deskripsi'   => 'Ruko pojok dengan area parkir mandiri di bagian depan. Memberikan privasi lebih bagi pelanggan Anda.',
                'status_unit' => 'kosong',
                'status'      => 'tersedia',
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