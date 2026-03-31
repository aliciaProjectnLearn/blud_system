<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class PenyewaSeeder extends Seeder
{
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

    public function run()
    {
        $dummyUsaha = [
            [
                'nama' => 'Budi Santoso',
                'usaha' => 'Warung Nasi Budi',
                'alamat' => 'Jl. Merdeka No. 10',
                'status_sewa' => 'aktif'
            ],
            [
                'nama' => 'Siti Aminah',
                'usaha' => 'Kantin Sehat Siti',
                'alamat' => 'Kampus Gedung A, Lt. 1',
                'status_sewa' => 'selesai'
            ],
            [
                'nama' => 'Andi Wijaya',
                'usaha' => 'Fotocopy & ATK Andi',
                'alamat' => 'Kantin Blok B',
                'status_sewa' => 'dibatalkan'
            ],
        ];

        DB::beginTransaction();

        try {
            // KATEGORI
            $kategoriId = DB::table('kategori')->value('id');
            if (!$kategoriId) {
                $kategoriId = DB::table('kategori')->insertGetId([
                    'nama' => 'Kantin',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            foreach ($dummyUsaha as $index => $data) {

                // 🔥 SKIP kalau dibatalkan (INI PENTING)
                if ($data['status_sewa'] == 'dibatalkan') {
                    continue;
                }

                // USER
                $userId = DB::table('users')->insertGetId([
                    'name'         => $data['nama'],
                    'username'     => 'penyewa_' . $index . '_' . rand(1000,9999),
                    'nama_lengkap' => $data['nama'],
                    'no_hp'        => '08' . rand(1000000000,9999999999),
                    'nik'          => rand(1000000000000000,9999999999999999),
                    'email'        => 'user'.$index.'@mail.com',
                    'password'     => Hash::make('password'),
                    'status_futsal'=> 'active',
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);

                // PENYEWA
                $penyewaId = DB::table('penyewa')->insertGetId([
                    'user_id'    => $userId,
                    'nama_usaha' => $data['usaha'],
                    'alamat'     => $data['alamat'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // RUKO (SELALU KOSONG, BIAR CONTROLLER YANG NGATUR)
                $kodeUnit = $this->generateNextKodeUnit();

                $rukoId = DB::table('ruko')->insertGetId([
                    'kode_unit'   => $kodeUnit,
                    'kategori_id' => $kategoriId,
                    'status_unit' => 'kosong',
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);

                // DOKUMENTASI UNIT (DUMMY)
                DB::table('dokumentasi_unit')->insert([
                    'ruko_id'       => $rukoId,
                    'file'          => 'dokumentasi_unit/sample.jpg',
                    'tipe'          => 'gambar',
                    'judul_dokumen' => 'Foto Unit ' . $kodeUnit,
                    'deskripsi'     => 'Dokumentasi awal unit',
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);

                // BOOKING (LANGSUNG IKUT STATUS)
                $bookingId = DB::table('booking')->insertGetId([
                    'user_id'    => $userId,
                    'status'     => $data['status_sewa'] == 'aktif' ? 'selesai' : $data['status_sewa'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // LOGIC TANGGAL
                $start = Carbon::now();
                $end = $data['status_sewa'] == 'selesai'
                    ? Carbon::now()->subDays(1)
                    : Carbon::now()->addYear();

                // SEWA RUKO
                $sewaId = DB::table('sewa_ruko')->insertGetId([
                    'booking_id'       => $bookingId,
                    'penyewa_id'       => $penyewaId,
                    'ruko_id'          => $rukoId,
                    'tgl_mulai'    => $start->format('Y-m-d'),
                    'tgl_selesai'  => $end->format('Y-m-d'),
                    'harga_sewa_tahunan' => 15000000,
                    'status'           => 'aktif', // nanti auto sync
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);

                // DOKUMEN MOU
                DB::table('tb_dokumen_penyewaan')->insert([
                    'no_mou'       => 'MOU-' . time() . '-' . $index,
                    'sewa_id'      => $sewaId,
                    'nama_dokumen' => 'contoh_mou.pdf',
                    'path_file'    => 'dokumen_mou/sample.pdf',
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            }

            DB::commit();
            $this->command->info('Seeder penyewaan + dokumen berhasil dijalankan!');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Seeder gagal: ' . $e->getMessage());
        }
    }
}