<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class PenyewaSeeder extends Seeder
{
    /**
     * Helper untuk menjana kode_unit mengikuti logika UnitController
     */
    private function generateNextKodeUnit()
    {
        $last = DB::table('ruko')
            ->whereNotNull('kode_unit')
            ->orderByRaw("CAST(SUBSTRING(kode_unit, 4) AS UNSIGNED) DESC")
            ->value('kode_unit');

        if ($last) {
            $lastNumber = (int) substr($last, 3);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return 'UNT' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dummyUsaha = [
            [
                'nama' => 'Budi Santoso', 
                'usaha' => 'Warung Nasi Budi', 
                'alamat' => 'Jl. Merdeka No. 10',
                'status_sewa' => 'disetujui',
                'status_unit' => 'terisi'
            ],
            [
                'nama' => 'Siti Aminah', 
                'usaha' => 'Kantin Sehat Siti', 
                'alamat' => 'Kampus Gedung A, Lt. 1',
                'status_sewa' => 'menunggu',
                'status_unit' => 'kosong'
            ],
            [
                'nama' => 'Andi Wijaya', 
                'usaha' => 'Fotocopy & ATK Andi', 
                'alamat' => 'Kantin Blok B',
                'status_sewa' => 'pending',
                'status_unit' => 'kosong'
            ],
        ];

        DB::beginTransaction();

        try {
            // 1. Ambil kategori sedia ada, jika tiada, cipta kategori baharu
            $kategoriId = DB::table('kategori')->value('id');
            if (!$kategoriId) {
                $kategoriId = DB::table('kategori')->insertGetId([
                    'nama' => 'Kantin',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }

            foreach ($dummyUsaha as $index => $data) {
                // Generate NIK 16 digit rawak
                $nik = mt_rand(10000000, 99999999) . mt_rand(10000000, 99999999);
                $randomSuffix = mt_rand(1000, 9999);

                // --- 2. Cipta Entiti USER ---
                $userId = DB::table('users')->insertGetId([
                    'name'         => $data['nama'],           
                    'username'     => 'penyewa_' . $index . '_' . $randomSuffix,
                    'nama_lengkap' => $data['nama'],
                    'no_hp'        => '08' . mt_rand(1000000000, 9999999999), 
                    'nik'          => (string) $nik,
                    'email'        => 'penyewa_' . $index . '_' . $randomSuffix . '@blud.com',
                    'password'     => Hash::make('password123'),
                    'status_futsal'=> 'active',
                    'created_at'   => Carbon::now(),
                    'updated_at'   => Carbon::now(),
                ]);

                // --- 3. Cipta Entiti PENYEWA ---
                $penyewaId = DB::table('penyewa')->insertGetId([
                    'user_id'    => $userId,
                    'nama_usaha' => $data['usaha'],
                    'alamat'     => $data['alamat'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                // --- 4. Cipta Entiti RUKO ---
                // Gunakan logika generate kode mengikuti UnitController
                $kodeUnit = $this->generateNextKodeUnit();
                
                $rukoId = DB::table('ruko')->insertGetId([
                    'kode_unit'   => $kodeUnit,
                    'kategori_id' => $kategoriId,
                    'status_unit' => $data['status_unit'],
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ]);

                // Tambah dokumentasi dummy mengikuti logika simpanDokumentasi di UnitController
                DB::table('dokumentasi_unit')->insert([
                    'ruko_id'       => $rukoId,
                    'file'          => 'dokumentasi_unit/dummy_sample.jpg',
                    'tipe'          => 'gambar',
                    'judul_dokumen' => 'Foto Unit ' . $kodeUnit,
                    'deskripsi'     => 'Foto dokumentasi unit awal untuk ' . $data['usaha'],
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ]);

                // --- 5. Cipta Entiti BOOKING ---
                $bookingId = DB::table('booking')->insertGetId([
                    'user_id'    => $userId,
                    'status'     => ($data['status_sewa'] == 'disetujui') ? 'selesai' : 'menunggu',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                // --- 6. Cipta Entiti SEWA RUKO ---
                DB::table('sewa_ruko')->insert([
                    'booking_id'          => $bookingId,
                    'penyewa_id'          => $penyewaId,
                    'ruko_id'             => $rukoId,
                    'tgl_mulai'           => Carbon::now()->format('Y-m-d'),
                    'tgl_selesai'         => Carbon::now()->addYear()->format('Y-m-d'),
                    'total_biaya_tahunan' => 15000000,
                    'no_mou'              => 'MOU/123/' . Carbon::now()->year . '/' . ($index + 1),
                    'status'              => $data['status_sewa'],
                    'created_at'          => Carbon::now(),
                    'updated_at'          => Carbon::now(),
                ]);
            }

            DB::commit();
            $this->command->info('Berjaya menjana 3 set data penyewa dengan pelbagai status mengikuti logika UnitController!');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Gagal menjalankan PenyewaSeeder: ' . $e->getMessage());
        }
    }
}
