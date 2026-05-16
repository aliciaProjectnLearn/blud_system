<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kategori;
use App\Models\Ruko;
use App\Models\SewaRuko;
use App\Models\PembayaranRuko;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class KantinRevisionSeeder extends Seeder
{
    public function run()
    {
        // Bersihkan data lama untuk menghindari duplikasi nomor HP/ID
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('pembayaran_ruko')->truncate();
        DB::table('sewa_ruko')->truncate();
        DB::table('ruko')->truncate();
        DB::table('kategori')->whereIn('id', [1, 2, 3])->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Buat Kategori
        $catBesar = Kategori::create(['id' => 1, 'nama' => 'Kantin Besar']);
        $catCont  = Kategori::create(['id' => 2, 'nama' => 'Kantin Container']);
        $catRuko  = Kategori::create(['id' => 3, 'nama' => 'Ruko Depan']);

        // 2. Buat Unit (Ruko)
        $units = [
            // Kantin Besar
            ['kode' => 'KNT-01', 'nama' => 'Kantin Utama 01', 'cat' => 1, 'harga' => 9000000, 'x' => 10, 'y' => 30],
            ['kode' => 'KNT-02', 'nama' => 'Kantin Utama 02', 'cat' => 1, 'harga' => 9000000, 'x' => 88, 'y' => 30],
            ['kode' => 'KNT-03', 'nama' => 'Kantin Utama 03', 'cat' => 1, 'harga' => 9000000, 'x' => 166, 'y' => 30],
            
            // Kantin Container
            ['kode' => 'CTR-01', 'nama' => 'Container A1', 'cat' => 2, 'harga' => 7000000, 'x' => 170, 'y' => 50],
            ['kode' => 'CTR-02', 'nama' => 'Container A2', 'cat' => 2, 'harga' => 7000000, 'x' => 580, 'y' => 60],
            ['kode' => 'CTR-03', 'nama' => 'Container B1', 'cat' => 2, 'harga' => 7000000, 'x' => 295, 'y' => 250],
            
            // Ruko Depan
            ['kode' => 'RK-01', 'nama' => 'Ruko Bisnis 01', 'cat' => 3, 'harga' => 20000000, 'x' => 50, 'y' => 50],
            ['kode' => 'RK-02', 'nama' => 'Ruko Bisnis 02', 'cat' => 3, 'harga' => 20000000, 'x' => 150, 'y' => 50],
        ];

        foreach ($units as $u) {
            Ruko::create([
                'kode_unit'   => $u['kode'],
                'nama_ruko'   => $u['nama'],
                'kategori_id' => $u['cat'],
                'harga'       => $u['harga'],
                'posisi_x'    => $u['x'],
                'posisi_y'    => $u['y'],
                'status'      => 'tersedia',
                'status_unit' => 'kosong', // Sinkronkan dengan accessor
            ]);
        }

        // 3. Buat User & Data Sewa Dummy yang Valid
        
        // Contoh 1: Sewa Aktif (KNT-01)
        $user1 = User::firstOrCreate(
            ['no_hp' => '081234567890'],
            [
                'name' => 'Budi Santoso', 
                'nama_lengkap' => 'Budi Santoso',
                'username' => 'budi0812',
                'email' => 'budi@example.com',
                'nik' => '3201010101010001', 
                'password' => bcrypt('password')
            ]
        );
        $ruko1 = Ruko::where('kode_unit', 'KNT-01')->first();
        $ruko1->update(['status' => 'disewa', 'status_unit' => 'terisi']);

        $sewa1 = SewaRuko::create([
            'user_id' => $user1->id,
            'ruko_id' => $ruko1->id,
            'access_token' => bin2hex(random_bytes(32)),
            'nama_penyewa' => $user1->name,
            'no_hp_snapshot' => $user1->no_hp,
            'nik_penyewa' => $user1->nik,
            'status_sewa' => 'aktif',
            'tanggal_mulai_sewa' => Carbon::now()->subMonths(2),
            'tanggal_selesai_sewa' => Carbon::now()->addMonths(10),
            'tipe_pembayaran' => '2_termin',
        ]);

        // Pembayaran Termin 1 (Lunas)
        PembayaranRuko::create([
            'sewa_ruko_id' => $sewa1->id,
            'termin_ke' => 1,
            'jumlah_tagihan' => $ruko1->harga / 2,
            'status_pembayaran' => 'dibayar',
            'tanggal_bayar' => Carbon::now()->subMonths(2),
            'tgl_jatuh_tempo' => Carbon::now()->subMonths(2),
        ]);
        // Pembayaran Termin 2 (Pending)
        PembayaranRuko::create([
            'sewa_ruko_id' => $sewa1->id,
            'termin_ke' => 2,
            'jumlah_tagihan' => $ruko1->harga / 2,
            'status_pembayaran' => 'pending',
            'tgl_jatuh_tempo' => Carbon::now()->addMonths(4),
        ]);

        // Contoh 2: Sewa Baru / Pending (CTR-01)
        $user2 = User::firstOrCreate(
            ['no_hp' => '089876543210'],
            [
                'name' => 'Siti Aminah', 
                'nama_lengkap' => 'Siti Aminah',
                'username' => 'siti0898',
                'email' => 'siti@example.com',
                'nik' => '3201010101010002', 
                'password' => bcrypt('password')
            ]
        );
        $ruko2 = Ruko::where('kode_unit', 'CTR-01')->first();
        $ruko2->update(['status' => 'disewa', 'status_unit' => 'terisi']);

        $sewa2 = SewaRuko::create([
            'user_id' => $user2->id,
            'ruko_id' => $ruko2->id,
            'access_token' => bin2hex(random_bytes(32)),
            'nama_penyewa' => $user2->name,
            'no_hp_snapshot' => $user2->no_hp,
            'nik_penyewa' => $user2->nik,
            'status_sewa' => 'pending',
            'tanggal_mulai_sewa' => Carbon::now()->addDays(7),
            'tanggal_selesai_sewa' => Carbon::now()->addDays(7)->addYear(),
            'tipe_pembayaran' => '1_termin',
        ]);

        PembayaranRuko::create([
            'sewa_ruko_id' => $sewa2->id,
            'termin_ke' => 1,
            'jumlah_tagihan' => $ruko2->harga,
            'status_pembayaran' => 'pending',
            'tgl_jatuh_tempo' => Carbon::now()->addDays(7),
        ]);
    }
}
