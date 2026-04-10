<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\LandingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminLogActivityController;
use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\SuperAdmin\TransaksiController;
use App\Http\Controllers\AdminFutsal\DashboardController as AdminFutsalDashboardController;
use App\Http\Controllers\AdminFutsal\PaketMembershipController;
use App\Http\Controllers\AdminFutsal\MembershipController;
use App\Http\Controllers\AdminFutsal\PelangganController;
use App\Http\Controllers\AdminFutsal\JadwalLapanganController;
use App\Http\Controllers\AdminFutsal\LaporanController;
use App\Http\Controllers\AdminFutsal\TransaksiController as AdminFutsalTransaksiController;
use App\Http\Controllers\AdminKantin\DashboardController as AdminKantinDashboardController;
use App\Http\Controllers\AdminKantin\UnitController as AdminKantinUnitController;
use App\Http\Controllers\AdminKantin\PembayaranController;
use App\Http\Controllers\AdminAc\DashboardController as AdminAcDashboardController;
use App\Http\Controllers\AdminAc\LayananController as AdminAcLayananController;
use App\Http\Controllers\AdminAc\TeknisiController as AdminAcTeknisiController;
use App\Http\Controllers\AdminAc\PelangganController as AdminAcPelangganController;
use App\Http\Controllers\AdminAc\BookingController as AdminAcBookingController;
use App\Http\Controllers\AdminAc\TransaksiController as AdminAcTransaksiController;
use App\Http\Controllers\User\DashboardUserController;



Route::middleware(['auth', 'role:Superadmin'])->group(function () {
    Route::get('/superadmin/monitoring', [AdminLogActivityController::class, 'index'])
        ->name('monitoring.index');
});

// route lain...
// ── Halaman Welcome ──────────────────────────────────────
Route::get('/', [LandingController::class, 'index'])->name('user.gateway');

Route::middleware(['auth'])->group(function () {
    // Rute Layanan Pelanggan (Placeholder)
    Route::prefix('user')->name('user.')->group(function () {

        Route::get('/futsal', function () {
            return 'Halaman Futsal Pelanggan (Belum dibuat)';
        })->name('futsal.index');
        // Route ruko dihapus karena digantikan oleh module kantin.

        Route::get('/futsal', [\App\Http\Controllers\User\FutsalDashboardController::class, 'landing'])->name('futsal.index');
        Route::get('/ruko', function () {
            return 'Halaman Ruko Pelanggan (Belum dibuat)';
        })->name('ruko.index');

        Route::get('/ac', function () {
            return 'Halaman AC Pelanggan (Belum dibuat)';
        })->name('ac.index');
    });
});

// ── Auth Routes (Login & Register) ───────────────────────
Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');
Route::get('/register', [AuthController::class, 'showRegister'])
    ->name('register');
Route::post('/register', [AuthController::class, 'register']);

// ── Protected Routes (perlu login) ───────────────────────
Route::middleware(['auth', 'role:Superadmin'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\SuperAdmin\DashboardController::class, 'index'])
        ->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    // User Management
    Route::resource('users', UserController::class);
    Route::get('/pelanggan', [UserController::class, 'indexPelanggan'])
        ->name('users.pelanggan');
    Route::get('/transaksi', [TransaksiController::class, 'index'])
        ->name('transaksi.index');
});

// ── Admin Futsal ───────────────────────
Route::middleware(['auth', 'role:Adminfutsal'])->prefix('adminfutsal')
    ->name('adminfutsal.')->group(function () {

        // Dashboard
        Route::get('/dashboard', [AdminFutsalDashboardController::class, 'index'])
            ->name('dashboard');

        // Paket Membership
        Route::resource('paket-membership', PaketMembershipController::class)
            ->only(['index', 'store', 'update', 'destroy']);

        // Monitoring Membership
        Route::get('monitoring-membership', [MembershipController::class, 'index'])
            ->name('monitoring-membership.index');
        Route::post('monitoring-membership', [MembershipController::class, 'store'])
            ->name('monitoring-membership.store');

        // Monitoring Pelanggan Reguler
        Route::get('pelanggan', [PelangganController::class, 'index'])
            ->name('pelanggan.index');
        Route::post('pelanggan', [PelangganController::class, 'store'])
            ->name('pelanggan.store');
        Route::put('pelanggan/{user}', [PelangganController::class, 'update'])
            ->name('pelanggan.update');


        // Frontend Jadwal Lapangan
        Route::get('jadwal-lapangan', [JadwalLapanganController::class, 'index'])
            ->name('jadwal-lapangan.index');

        // Jadwal Lapangan API Internal
        Route::get('jadwal-lapangan/api-by-tanggal', [JadwalLapanganController::class, 'getJadwalByTanggal'])
            ->name('jadwal-lapangan.api');
        Route::post('jadwal-lapangan/booking', [JadwalLapanganController::class, 'updateStatusBooking'])
            ->name('jadwal-lapangan.booking');
        Route::post('jadwal-lapangan/batal', [JadwalLapanganController::class, 'batalBooking'])
            ->name('jadwal-lapangan.batal');

        // Manajemen Booking Futsal
        Route::get('booking/jadwal-tersedia', [\App\Http\Controllers\AdminFutsal\BookingController::class, 'getAvailableSlots'])
            ->name('booking.jadwal_tersedia');
        Route::get('booking', [\App\Http\Controllers\AdminFutsal\BookingController::class, 'index'])
            ->name('booking.index');
        Route::post('booking', [\App\Http\Controllers\AdminFutsal\BookingController::class, 'store'])
            ->name('booking.store');
        Route::patch('booking/{id}', [\App\Http\Controllers\AdminFutsal\BookingController::class, 'update'])
            ->name('booking.update');
        Route::patch('booking/{id}/cancel', [\App\Http\Controllers\AdminFutsal\BookingController::class, 'cancel'])
            ->name('booking.cancel');
        Route::patch('booking/{id}/selesai', [\App\Http\Controllers\AdminFutsal\BookingController::class, 'selesai'])
            ->name('booking.selesai');

        // Manajemen Transaksi Futsal
        Route::get('transaksi', [AdminFutsalTransaksiController::class, 'index'])
            ->name('transaksi.index');
        Route::get('transaksi/{id}', [AdminFutsalTransaksiController::class, 'show'])
            ->name('transaksi.show');
        Route::patch('transaksi/{id}/konfirmasi', [AdminFutsalTransaksiController::class, 'konfirmasi'])
            ->name('transaksi.konfirmasi');

        // Laporan Transaksi Futsal
        Route::get('laporan', [LaporanController::class, 'index'])
            ->name('laporan.index');
        Route::get('laporan/export-pdf', [LaporanController::class, 'exportPdf'])
            ->name('laporan.export.pdf');
        Route::get('laporan/export-excel', [LaporanController::class, 'exportExcel'])
            ->name('laporan.export.excel');

        // Pengaturan
        Route::get('pengaturan', [\App\Http\Controllers\AdminFutsal\PengaturanController::class, 'index'])
            ->name('pengaturan.index');
        Route::put('pengaturan/{id}', [\App\Http\Controllers\AdminFutsal\PengaturanController::class, 'update'])
            ->name('pengaturan.update');
    });


// ── Admin Kantin ───────────────────────
Route::middleware(['auth', 'role:Adminkantin'])->prefix('adminkantin')->name('adminkantin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [AdminKantinDashboardController::class, 'index'])
        ->name('dashboard');

    // Manajemen Unit Kantin
    Route::resource('unit', AdminKantinUnitController::class);

    // AJAX: Edit detail dokumen unit (Nama & Tipe)
    Route::patch('dokumentasi/{dokumen}/detail', [AdminKantinUnitController::class, 'updateDokumenDetail'])
        ->name('unit.dokumen.updateDetail');

    // Manajemen Penyewa Kantin/Ruko
    Route::resource('penyewa', \App\Http\Controllers\AdminKantin\PenyewaController::class)->except(['create', 'store']);

    // Manajemen Penyewaan Kantin/Ruko
    Route::resource('penyewaan', \App\Http\Controllers\AdminKantin\PenyewaanController::class);
    Route::post('penyewaan/{id}/upload-dokumen', [\App\Http\Controllers\AdminKantin\PenyewaanController::class, 'uploadDokumen'])
        ->name('penyewaan.uploadDokumen');
    Route::get('dokumen-penyewaan/{id}/download', [\App\Http\Controllers\AdminKantin\PenyewaanController::class, 'downloadDokumen'])
        ->name('penyewaan.downloadDokumen');
    Route::delete('dokumen-penyewaan/{id}', [\App\Http\Controllers\AdminKantin\PenyewaanController::class, 'hapusDokumen'])
        ->name('penyewaan.hapusDokumen');
    Route::get('penyewaan/{id}/generate-mou', [\App\Http\Controllers\AdminKantin\PenyewaanController::class, 'generateMOU'])
        ->name('penyewaan.generateMOU');

    // Pembayaran Kantin
    Route::get('pembayaran', [PembayaranController::class, 'index'])
        ->name('pembayaran.index');
    Route::get('pembayaran/{pembayaran}', [PembayaranController::class, 'show'])
        ->name('pembayaran.show');
    Route::put('pembayaran/{pembayaran}', [PembayaranController::class, 'update'])
        ->name('pembayaran.update');
    Route::get('pembayaran/{pembayaran}/kwitansi', [PembayaranController::class, 'downloadKwitansi'])
        ->name('pembayaran.kwitansi');

    // Laporan Transaksi Kantin/Ruko
    Route::get('laporan', [\App\Http\Controllers\AdminKantin\LaporanController::class, 'index'])
        ->name('laporan.index');
    Route::get('laporan/export-pdf', [\App\Http\Controllers\AdminKantin\LaporanController::class, 'exportPdf'])
        ->name('laporan.export.pdf');
    Route::get('laporan/export-excel', [\App\Http\Controllers\AdminKantin\LaporanController::class, 'exportExcel'])
        ->name('laporan.export.excel');
});


// ── Admin AC ───────────────────────
Route::middleware(['auth', 'role:Adminac'])->prefix('adminac')->name('adminac.')->group(function () {
    Route::get('/dashboard', [AdminAcDashboardController::class, 'index'])
        ->name('dashboard');
    Route::post('kategori', [AdminAcLayananController::class, 'storeKategori'])
        ->name('kategori.store');
    Route::post('kategori-komponen', [\App\Http\Controllers\AdminAc\ProdukController::class, 'storeKategori'])
        ->name('kategori_komponen.store');
    Route::resource('layanan', AdminAcLayananController::class);
    Route::resource('teknisi', AdminAcTeknisiController::class);
    Route::get('teknisi/{id}/cek-ketersediaan', [AdminAcTeknisiController::class, 'cekKetersediaan'])
        ->name('teknisi.cek_ketersediaan');
    Route::resource('produk', \App\Http\Controllers\AdminAc\ProdukController::class)
        ->except(['create', 'show', 'edit']);
    Route::patch('produk/{produk}/stok', [\App\Http\Controllers\AdminAc\ProdukController::class, 'updateStok'])
        ->name('produk.updateStok');

    // Manajemen Pelanggan
    Route::get('pelanggan', [AdminAcPelangganController::class, 'index'])
        ->name('pelanggan.index');
    Route::get('pelanggan/{id}', [AdminAcPelangganController::class, 'show'])
        ->name('pelanggan.show');

    // Manajemen Booking
    Route::get('booking', [AdminAcBookingController::class, 'index'])
        ->name('booking.index');
    Route::post('booking/{id}/approve', [AdminAcBookingController::class, 'approve'])
        ->name('booking.approve');
    Route::patch('booking/{id}/selesai', [AdminAcBookingController::class, 'selesai'])
        ->name('booking.selesai');
    Route::get('booking-teknisi-tersedia', [AdminAcBookingController::class, 'teknisiTersedia'])
        ->name('booking.teknisi_tersedia');

    // Manajemen Transaksi
    Route::get('transaksi/histori', [AdminAcTransaksiController::class, 'history'])
        ->name('transaksi.history');
    Route::resource('transaksi', AdminAcTransaksiController::class);
    Route::patch('transaksi/{id}/status', [AdminAcTransaksiController::class, 'updateStatus'])
        ->name('transaksi.update_status');

    // Laporan Transaksi AC
    Route::get('laporan', [\App\Http\Controllers\AdminAc\LaporanController::class, 'index'])
        ->name('laporan.index');
    Route::get('laporan/export-pdf', [\App\Http\Controllers\AdminAc\LaporanController::class, 'exportPdf'])
        ->name('laporan.export.pdf');
    Route::get('laporan/export-excel', [\App\Http\Controllers\AdminAc\LaporanController::class, 'exportExcel'])
        ->name('laporan.export.excel');
});

// Rute untuk Pelanggan (User Dashboard)
Route::middleware(['auth', 'role:Pelanggan'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', [DashboardUserController::class, 'index'])->name('dashboard');

    // Profile Management
    Route::get('/profile', [App\Http\Controllers\User\ProfileUserController::class, 'index'])->name('profile.index');
    Route::put('/profile', [App\Http\Controllers\User\ProfileUserController::class, 'update'])->name('profile.update');

    // Futsal Management
    Route::prefix('futsal')->name('futsal.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\User\FutsalDashboardController::class, 'index'])->name('dashboard');
        Route::get('/history', [\App\Http\Controllers\User\FutsalDashboardController::class, 'history'])->name('history');

        // Booking Lapangan
        Route::get('/booking', [\App\Http\Controllers\User\FutsalDashboardController::class, 'showBookingForm'])->name('booking.form');
        Route::post('/booking', [\App\Http\Controllers\User\FutsalDashboardController::class, 'store'])->name('booking.store');
        Route::get('/check-availability', [\App\Http\Controllers\User\FutsalDashboardController::class, 'checkAvailability'])->name('booking.check');

        // API-like endpoints
        Route::prefix('api')->name('api.')->group(function () {
            Route::get('/summary', [\App\Http\Controllers\User\FutsalDashboardController::class, 'getSummary'])->name('summary');
            Route::get('/recent', [\App\Http\Controllers\User\FutsalDashboardController::class, 'getRecentBookings'])->name('recent');
            Route::get('/history-data', [\App\Http\Controllers\User\FutsalDashboardController::class, 'getHistoryData'])->name('history-data');
            Route::get('/booking/{id}', [\App\Http\Controllers\User\FutsalDashboardController::class, 'getDetail'])->name('detail');
        });

        Route::get('/booking/{id}/invoice', [\App\Http\Controllers\User\FutsalDashboardController::class, 'showInvoice'])->name('invoice');
        Route::get('/booking/{id}/download-invoice', [\App\Http\Controllers\User\FutsalDashboardController::class, 'downloadInvoice'])->name('download-invoice');
    });

    // Kantin Manajemen
    Route::prefix('kantin')->name('kantin.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\User\KantinDashboardController::class, 'index'])->name('dashboard');
        Route::get('/tagihan', [\App\Http\Controllers\User\KantinDashboardController::class, 'tagihan'])->name('tagihan');
        Route::get('/riwayat', [\App\Http\Controllers\User\KantinDashboardController::class, 'riwayat'])->name('riwayat');
        Route::get('/katalog', [\App\Http\Controllers\User\KantinDashboardController::class, 'pilihUnit'])->name('katalog');
        Route::get('/booking/{id}', [\App\Http\Controllers\User\KantinDashboardController::class, 'formSewa'])->name('booking');
        Route::post('/booking/{id}', [\App\Http\Controllers\User\KantinDashboardController::class, 'storeSewa'])->name('store_booking');
    });

    // AC Manajemen
    Route::prefix('ac')->name('ac.')->group(function () {
        Route::get('/', [\App\Http\Controllers\User\AcDashboardController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\User\AcDashboardController::class, 'store'])->name('store');
        Route::get('/layanan', [\App\Http\Controllers\User\AcDashboardController::class, 'layanan'])->name('layanan');
        Route::get('/history', [\App\Http\Controllers\User\AcDashboardController::class, 'history'])->name('history');
        Route::get('/{id}', [\App\Http\Controllers\User\AcDashboardController::class, 'show'])->name('show');
        Route::post('/{id}/cancel', [\App\Http\Controllers\User\AcDashboardController::class, 'cancel'])->name('cancel');
    });
});

// ── Teknisi AC ───────────────────────
Route::middleware(['auth', 'role:Teknisi'])->prefix('teknisi')->name('teknisi.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\TeknisiAc\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/pekerjaan/{id}', [\App\Http\Controllers\TeknisiAc\DashboardController::class, 'show'])->name('pekerjaan.show');
    Route::post('/pekerjaan/{id}/selesai', [\App\Http\Controllers\TeknisiAc\DashboardController::class, 'selesaikanPekerjaan'])->name('pekerjaan.selesai');
});
