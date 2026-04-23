<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ServisKendaraanSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Gunakan DB Transaction untuk menjaga integritas relasi antar tabel (otomatis revert jika gagal)
        DB::beginTransaction();

        try {
            $now = Carbon::now();

            // ===============================================
            // 1. DATA MASTER TEKNISI, KASIR & ROLE
            // ===============================================

            // A. Pengecekan / Pembuatan Roles
            $roleIds = [];
            foreach (['Teknisi Motor', 'Teknisi Mobil', 'Kasir', 'Pelanggan'] as $roleName) {
                $role = DB::table('roles')->where('nama', $roleName)->first();
                if (!$role) {
                    $roleIds[$roleName] = DB::table('roles')->insertGetId([
                        'nama' => $roleName, 'created_at' => $now, 'updated_at' => $now
                    ]);
                } else {
                    $roleIds[$roleName] = $role->id;
                }
            }

            // B. User Kasir Baru
            $kasirId = DB::table('users')->where('username', 'kasir_bengkel')->value('id');
            if (!$kasirId) {
                $kasirId = DB::table('users')->insertGetId([
                    'name' => 'Kasir',
                    'username' => 'kasir_bengkel',
                    'email' => 'kasir@bengkel.com',
                    'password' => Hash::make('password'),
                    'nama_lengkap' => 'Kasir',
                    'no_hp' => '08999999999',
                    'spesialisasi' => null, // Kasir tanpa spesialisasi
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
                DB::table('roles_users')->insert(['user_id' => $kasirId, 'role_id' => $roleIds['Kasir'], 'created_at' => $now, 'updated_at' => $now]);
            }

            // C. User Pelanggan Dummy
            $pelangganId = DB::table('users')->where('username', 'pelanggan_setia')->value('id');
            if (!$pelangganId) {
                $pelangganId = DB::table('users')->insertGetId([
                    'name' => 'Budi Pelanggan',
                    'username' => 'pelanggan_setia',
                    'email' => 'budi_pelanggan@test.com',
                    'password' => Hash::make('password'),
                    'nama_lengkap' => 'Budi Sudarsono',
                    'no_hp' => '08123123123',
                    'spesialisasi' => null,
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
                DB::table('roles_users')->insert(['user_id' => $pelangganId, 'role_id' => $roleIds['Pelanggan'], 'created_at' => $now, 'updated_at' => $now]);
            }

            // D. Penambahan Teknisi (Motor & Mobil) Menggunakan Role Terpisah
            $teknisiList = [
                ['name' => 'Ahli Motor 1', 'username' => 'tek_motor1', 'spesialisasi' => 'motor', 'roleName' => 'Teknisi Motor'],
                ['name' => 'Ahli Mobil 1', 'username' => 'tek_mobil1', 'spesialisasi' => 'mobil', 'roleName' => 'Teknisi Mobil'],
            ];

            $teknisiIds = []; 
            foreach ($teknisiList as $t) {
                $id = DB::table('users')->where('username', $t['username'])->value('id');
                if (!$id) {
                    $id = DB::table('users')->insertGetId([
                        'name' => $t['name'],
                        'username' => $t['username'],
                        'email' => $t['username'] . '@bengkel.com',
                        'password' => Hash::make('password'),
                        'nama_lengkap' => $t['name'],
                        'no_hp' => '087700012345',
                        'spesialisasi' => $t['spesialisasi'],
                        'created_at' => $now,
                        'updated_at' => $now
                    ]);
                    DB::table('roles_users')->insert(['user_id' => $id, 'role_id' => $roleIds[$t['roleName']], 'created_at' => $now, 'updated_at' => $now]);
                }
                $teknisiIds[$t['spesialisasi']] = $id; 
            }

            // ===============================================
            // 2. DATA MASTER LAYANAN
            // ===============================================
            $layanans = [
                ['nama_layanan' => 'Servis & Ganti Oli Motor', 'harga_estimasi' => 50000, 'tipe_kendaraan' => 'motor'],
                ['nama_layanan' => 'Tune Up Injeksi Mobil', 'harga_estimasi' => 350000, 'tipe_kendaraan' => 'mobil'],
            ];

            $layananIds = [];
            foreach ($layanans as $l) {
                $check = DB::table('layanan_servis')->where('nama_layanan', $l['nama_layanan'])->first();
                if (!$check) {
                    $id = DB::table('layanan_servis')->insertGetId(array_merge($l, ['created_at' => $now, 'updated_at' => $now]));
                    $layananIds[$l['tipe_kendaraan']] = $id;
                } else {
                    $layananIds[$l['tipe_kendaraan']] = $check->id;
                }
            }

            // ===============================================
            // 3. TRANSAKSI SERVIS SAMPAI PAYMENT
            // ===============================================
            $kasirName = DB::table('users')->where('id', $kasirId)->value('nama_lengkap');

            // --- TRANSAKSI 1: SERVIS MOTOR ---
            $bkMotorId = DB::table('booking_servis')->insertGetId([
                'kode_booking' => 'MTR-'.Str::upper(Str::random(6)),
                'user_id' => $pelangganId,
                'layanan_servis_id' => $layananIds['motor'],
                'tipe_kendaraan' => 'motor',
                'merek_kendaraan' => 'Honda Vario 150',
                'nomor_plat' => 'B 1111 MTR',
                'tahun_kendaraan' => 2021,
                'keluhan' => 'Ganti oli rutin',
                'tanggal_booking' => $now->toDateString(),
                'jam_booking' => '09:00:00',
                'status' => 'selesai',
                'teknisi_id' => $teknisiIds['motor'], // Spesialis Motor
                'created_at' => $now, 'updated_at' => $now
            ]);

            // Item Rincian (Motor)
            DB::table('rincian_servis')->insert([
                [
                    'booking_servis_id' => $bkMotorId, 'kategori_komponen_id' => null, 'nama_item' => 'Jasa Ganti Oli', 'jumlah' => 1, 'harga_satuan' => 20000, 'subtotal' => 20000, 'created_at' => $now, 'updated_at' => $now
                ],
                [
                    'booking_servis_id' => $bkMotorId, 'kategori_komponen_id' => null, 'nama_item' => 'Oli Mesin MPX 2', 'jumlah' => 1, 'harga_satuan' => 50000, 'subtotal' => 50000, 'created_at' => $now, 'updated_at' => $now
                ]
            ]);

            // Pembayaran Motor -> Diproses Kasir
            DB::table('pembayaran_servis')->insert([
                'booking_servis_id' => $bkMotorId,
                'kode_pembayaran' => 'PAY-M-'.Str::upper(Str::random(5)),
                'total_biaya' => 70000,
                'tipe_pembayaran' => 'tunai',
                'status_pembayaran' => 'lunas',
                'tanggal_bayar' => $now,
                'catatan' => 'Pembayaran lunas diproses oleh Kasir: ' . $kasirName,
                'created_at' => $now, 'updated_at' => $now
            ]);

            // --- TRANSAKSI 2: SERVIS MOBIL ---
            $bkMobilId = DB::table('booking_servis')->insertGetId([
                'kode_booking' => 'MBL-'.Str::upper(Str::random(6)),
                'user_id' => $pelangganId,
                'layanan_servis_id' => $layananIds['mobil'],
                'tipe_kendaraan' => 'mobil',
                'merek_kendaraan' => 'Toyota Avanza',
                'nomor_plat' => 'B 9999 MOB',
                'tahun_kendaraan' => 2019,
                'keluhan' => 'Tarikan berat dan brebet',
                'tanggal_booking' => $now->toDateString(),
                'jam_booking' => '13:00:00',
                'status' => 'selesai',
                'teknisi_id' => $teknisiIds['mobil'], // Spesialis Mobil
                'created_at' => $now, 'updated_at' => $now
            ]);

            // Item Rincian (Mobil)
            DB::table('rincian_servis')->insert([
                [
                    'booking_servis_id' => $bkMobilId, 'kategori_komponen_id' => null, 'nama_item' => 'Jasa Tune Up', 'jumlah' => 1, 'harga_satuan' => 350000, 'subtotal' => 350000, 'created_at' => $now, 'updated_at' => $now
                ],
                [
                    'booking_servis_id' => $bkMobilId, 'kategori_komponen_id' => null, 'nama_item' => 'Busi Platinum', 'jumlah' => 4, 'harga_satuan' => 40000, 'subtotal' => 160000, 'created_at' => $now, 'updated_at' => $now
                ]
            ]);

            // Pembayaran Mobil -> Diproses Kasir
            DB::table('pembayaran_servis')->insert([
                'booking_servis_id' => $bkMobilId,
                'kode_pembayaran' => 'PAY-B-'.Str::upper(Str::random(5)),
                'total_biaya' => 510000,
                'tipe_pembayaran' => 'qris',
                'status_pembayaran' => 'lunas',
                'tanggal_bayar' => $now,
                'catatan' => 'Pembayaran lunas dicek dan diproses oleh Kasir: ' . $kasirName,
                'created_at' => $now, 'updated_at' => $now
            ]);

            // ===============================================
            // 4. PENGELUARAN SERVIS
            // ===============================================
            DB::table('pengeluaran_servis')->insert([
                [
                    'tanggal' => $now->toDateString(),
                    'keterangan' => 'Pembelian kunci pas set baru',
                    'jumlah' => 150000,
                    'kategori' => 'sparepart',
                    'created_by' => $kasirId,
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'tanggal' => $now->copy()->subDays(2)->toDateString(),
                    'keterangan' => 'Uang makan teknisi lembur',
                    'jumlah' => 50000,
                    'kategori' => 'operasional',
                    'created_by' => $kasirId,
                    'created_at' => $now->copy()->subDays(2),
                    'updated_at' => $now->copy()->subDays(2)
                ]
            ]);

            // Commit final apabila semua Insert sukses!
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
