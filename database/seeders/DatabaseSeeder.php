<?php

namespace Database\Seeders;

use Database\Seeders\RoleTableSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(LogActivityTableSeeder::class);
        // Tabel master (tanpa foreign key)
        $this->call(RoleTableSeeder::class);           // asumsi ada RoleTableSeeder
        $this->call(UsersTableSeeder::class);
        $this->call(TipePembayaranTableSeeder::class);
        $this->call(KategoriTableSeeder::class);
        $this->call(KategoriKomponenSeeder::class);
        $this->call(ProdukSeeder::class);
        $this->call(LapanganTableSeeder::class);
        $this->call(JamOperasionalSeeder::class);
        $this->call(JadwalLapanganSeeder::class);
        $this->call(PengaturanTableSeeder::class);

        // Tabel dengan foreign key ke tabel di atas
        $this->call(RolesUsersTableSeeder::class); // Aktifkan kembali agar role base system (User ID 1-11) tidak hilang
        $this->call(RukoTableSeeder::class);
        $this->call(LayananAcTableSeeder::class);     // pastikan nama tepat
        $this->call(PaketMembershipSeeder::class);
        $this->call(MembershipTableSeeder::class);


        // Booking (tergantung users)
        $this->call(BookingTableSeeder::class);       // pastikan ada BookingTableSeeder, bukan Bookings

        // Detail booking
        $this->call(BookingFutsalTableSeeder::class);
        $this->call(BookingAcTableSeeder::class);
        $this->call(PenyewaSeeder::class);           // penyewa (relasi ke users yang sudah ada)
        // $this->call(SewaRukoTableSeeder::class);      // sewa_ruko (truncate lama, buat data konsisten)

        // Fasilitas dan detail lainnya
        $this->call(FasilitasLapanganTableSeeder::class);
        $this->call(DetailServisTableSeeder::class);
        $this->call(PembatalanTableSeeder::class);

        // Pembayaran
        $this->call(PembayaranFutsalTableSeeder::class);
        $this->call(PembayaranAcTableSeeder::class);
        // $this->call(PembayaranRukoTableSeeder::class);

        //pengeluaran
        $this->call(PengeluaranFutsalsSeeder::class);
        $this->call(PengeluaranAcSeeder::class);
        $this->call(PengeluaranKantinSeeder::class);

        // Modul Servis Motor & Mobil Terpadu
        $this->call(MerekModelKendaraanSeeder::class);
        $this->call(ServisKendaraanSeeder::class);

        // Modul Teknisi Servis
        $this->call(TeknisiServisSeeder::class);

        // Kasir Futsal
        $this->call(KasirFutsalUserSeeder::class);
    }
}
