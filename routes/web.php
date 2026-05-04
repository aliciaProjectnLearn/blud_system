<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\LandingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminLogActivityController;
use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\SuperAdmin\TransaksiController;
use App\Http\Controllers\SuperAdmin\RekapKeuanganController;
use App\Http\Controllers\AdminFutsal\DashboardController as AdminFutsalDashboardController;
use App\Http\Controllers\SuperAdmin\PembagianPendapatanController;
use App\Http\Controllers\AdminFutsal\PaketMembershipController;
use App\Http\Controllers\AdminFutsal\MembershipController;
use App\Http\Controllers\AdminFutsal\PelangganController;
use App\Http\Controllers\AdminFutsal\JadwalLapanganController;
use App\Http\Controllers\AdminFutsal\LaporanController;
use App\Http\Controllers\AdminFutsal\TransaksiController as AdminFutsalTransaksiController;
use App\Http\Controllers\AdminFutsal\KeuanganController;
use App\Http\Controllers\AdminKantin\DashboardController as AdminKantinDashboardController;
use App\Http\Controllers\AdminKantin\UnitController as AdminKantinUnitController;
use App\Http\Controllers\AdminKantin\PembayaranController;
use App\Http\Controllers\AdminAc\DashboardController as AdminAcDashboardController;
use App\Http\Controllers\AdminAc\LayananController as AdminAcLayananController;
use App\Http\Controllers\AdminAc\TeknisiController as AdminAcTeknisiController;
use App\Http\Controllers\AdminAc\PelangganController as AdminAcPelangganController;
use App\Http\Controllers\AdminAc\BookingController as AdminAcBookingController;
use App\Http\Controllers\AdminAc\TransaksiController as AdminAcTransaksiController;
use App\Http\Controllers\KasirServis\DashboardController as KasirDashboardController;
use App\Http\Controllers\User\DashboardUserController;
use App\Http\Controllers\KasirServis\BookingKasirController;
use App\Http\Controllers\User\UserServisController;
use App\Http\Controllers\User\CekBookingController;
use App\Http\Controllers\User\OtpController;

Route::get('/', [LandingController::class, 'index'])->name('home');

Route::prefix('user')->name('user.')->group(function () {
    Route::get('/', [LandingController::class, 'index'])->name('gateway');

    // Fitur Cek Booking via Nomor HP (Poin 2)
    Route::post('/cek-booking/otp', [CekBookingController::class, 'requestOtp'])->name('cek-booking.otp')->middleware('throttle:10,1');
    Route::post('/cek-booking/verify', [CekBookingController::class, 'verifyOtp'])->name('cek-booking.verify')->middleware('throttle:10,1');
    
    // Revised Cek Booking Routes (Modal Flow) - Hardened with Throttle
    Route::middleware(['throttle:5,1'])->group(function () {
        Route::post('/cek-booking/kirim-otp', [CekBookingController::class, 'kirimOtp'])->name('cek.booking.kirim-otp');
    });

    Route::middleware(['throttle:10,1'])->group(function () {
        Route::post('/cek-booking/verifikasi', [CekBookingController::class, 'verifikasi'])->name('cek.booking.verifikasi');
    });
    
    Route::get('/cek-booking/riwayat', [CekBookingController::class, 'riwayat'])->name('cek.booking.riwayat');
    
    // Token-Based Access (Detail, Pembatalan) — Riwayat dihapus (Poin 2)
    Route::get('/access/{token}', [App\Http\Controllers\User\TokenAccessController::class, 'show'])->name('token.show');
    // Route::get('/access/{token}/history', ...) — Dihapus per Poin 2
    Route::get('/access/{token}/cancel', [App\Http\Controllers\User\TokenAccessController::class, 'batalkan'])->name('token.batalkan');
    Route::post('/access/{token}/cancel', [App\Http\Controllers\User\TokenAccessController::class, 'prosesBatalkan'])->name('token.batalkan.proses');

    // OTP verification routes (Poin 6 & 7)
    Route::post('/access/{token}/verify-otp', [App\Http\Controllers\User\TokenAccessController::class, 'verifyOtp'])
         ->name('token.otp.verify')->middleware('throttle:10,1');
    Route::post('/access/{token}/resend-otp', [App\Http\Controllers\User\TokenAccessController::class, 'resendOtp'])
         ->name('token.otp.resend');

    // Futsal
    Route::prefix('futsal')->name('futsal.')->group(function () {
        Route::get('/landing', [App\Http\Controllers\User\FutsalBookingController::class, 'landing'])->name('landing');
        Route::get('/booking', [App\Http\Controllers\User\FutsalBookingController::class, 'index'])->name('booking.form');
        Route::post('/booking', [App\Http\Controllers\User\FutsalBookingController::class, 'store'])->name('booking.store');
        Route::get('/beli-paket', [App\Http\Controllers\User\FutsalBookingController::class, 'paketForm'])->name('paket.form');
        Route::post('/beli-paket', [App\Http\Controllers\User\FutsalBookingController::class, 'paketStore'])->name('paket.store');
        Route::get('/check-availability', [App\Http\Controllers\User\FutsalBookingController::class, 'checkAvailability'])->name('booking.check');
        Route::get('/api/check-membership', [App\Http\Controllers\User\FutsalBookingController::class, 'checkMembership'])->name('api.check.membership');
        Route::post('/check-booking-aktif', [App\Http\Controllers\User\FutsalBookingController::class, 'checkBookingAktif'])->name('check.booking.aktif');
    });

    Route::prefix('kantin')->name('kantin.')->group(function () {
        Route::get('/', [\App\Http\Controllers\User\KantinController::class, 'index'])->name('index');
        Route::get('/katalog', [\App\Http\Controllers\User\KantinController::class, 'katalog'])->name('katalog');
        Route::get('/booking/{ruko_id}', [\App\Http\Controllers\User\KantinController::class, 'formBooking'])->name('booking');
        Route::post('/booking', [\App\Http\Controllers\User\KantinController::class, 'simpanBooking'])->name('booking.store');
        Route::post('/check-phone', [\App\Http\Controllers\User\KantinController::class, 'checkPhone'])->name('check-phone');
        Route::get('/cek-hp', [\App\Http\Controllers\User\KantinController::class, 'cekHp'])->name('cek.hp');
        Route::post('/check-nik', [\App\Http\Controllers\User\KantinController::class, 'checkNik'])->name('check-nik');
        Route::get('/unit/{id}/detail', [\App\Http\Controllers\User\KantinController::class, 'unitDetail'])->name('unit.detail');

        // OTP & Session flow
        Route::prefix('otp')->name('otp.')->group(function () {
            Route::get('/{token}', [OtpController::class, 'form'])->name('form');
            Route::post('/{token}/verifikasi', [OtpController::class, 'verifikasi'])->name('verifikasi');
            Route::get('/{token}/kirim-ulang', [OtpController::class, 'kirimUlang'])->name('kirim-ulang');
        });
        Route::get('/sewa/{token}', [\App\Http\Controllers\User\SewaTokenController::class, 'detail'])->name('sewa.detail');
        Route::get('/sewa/{token}/riwayat', [\App\Http\Controllers\User\SewaTokenController::class, 'riwayat'])->name('sewa.riwayat');
        Route::get('/sewa/{token}/pembayaran', [\App\Http\Controllers\User\SewaTokenController::class, 'pembayaran'])->name('sewa.pembayaran');
        Route::post('/sewa/{token}/upload-bukti', [\App\Http\Controllers\User\SewaTokenController::class, 'uploadBukti'])->name('sewa.upload');
        Route::get('/sewa/{token}/dokumen', [\App\Http\Controllers\User\SewaTokenController::class, 'dokumen'])->name('sewa.dokumen');
        Route::post('/sewa/{token}/batalkan', [\App\Http\Controllers\User\SewaTokenController::class, 'batalkan'])->name('sewa.batalkan');
    });

    Route::prefix('ac')->name('ac.')->group(function () {
        Route::get('/', [App\Http\Controllers\User\AcBookingController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\User\AcBookingController::class, 'store'])->name('store');
        Route::get('/layanan', [App\Http\Controllers\User\AcBookingController::class, 'layanan'])->name('layanan');
        
        // Token Based Access
        Route::get('/booking/{token}', [App\Http\Controllers\User\AcTokenController::class, 'show'])->name('token.show');
        Route::get('/booking/{token}/otp', [App\Http\Controllers\User\AcTokenController::class, 'otpForm'])->name('token.otp');
        Route::post('/booking/{token}/otp', [App\Http\Controllers\User\AcTokenController::class, 'verifyOtp'])->name('token.otp.verify');
        Route::post('/booking/{token}/resend', [App\Http\Controllers\User\AcTokenController::class, 'resendOtp'])->name('token.otp.resend');
      
        // Token Based Access
        Route::get('/booking/{token}', [App\Http\Controllers\User\AcTokenController::class, 'show'])->name('token.show');
        Route::get('/booking/{token}/history', [App\Http\Controllers\User\AcTokenController::class, 'riwayat'])->name('token.riwayat');

    });

    Route::prefix('servis')->name('servis.')->group(function () {
        Route::get('/katalog', [App\Http\Controllers\User\UserServisController::class, 'katalog'])->name('katalog');
        Route::get('/booking', [App\Http\Controllers\User\UserServisController::class, 'booking'])->name('booking');
        Route::post('/store', [App\Http\Controllers\User\UserServisController::class, 'store'])->name('store');
        Route::get('/slots', [App\Http\Controllers\User\UserServisController::class, 'getSlot'])->name('slots');
        Route::get('/sukses/{token}', [App\Http\Controllers\User\UserServisController::class, 'sukses'])->name('sukses');
        
        // Token Based Access for Vehicle Service
        Route::get('/access/{token}', [App\Http\Controllers\User\UserServisController::class, 'detailToken'])->name('token.show');
        Route::get('/access/{token}/cancel', [App\Http\Controllers\User\UserServisController::class, 'batalkan'])->name('token.batalkan');
        Route::post('/access/{token}/otp/verify', [App\Http\Controllers\User\UserServisController::class, 'verifyOtp'])->name('token.otp.verify');
        Route::post('/access/{token}/otp/resend', [App\Http\Controllers\User\UserServisController::class, 'resendOtp'])->name('token.otp.resend');
    });
});

// ==========================================
// JALUR INTERNAL — Dengan Auth
// ==========================================

// Login Page Internal Staff (Unique URL for Security)
Route::get('/internal-staff-blud-access-v1', [AuthController::class, 'showLogin'])->name('staff.login');

Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// 1. ADMIN (Superadmin, Admin Futsal, Admin Kantin, Admin AC, Admin Servis)
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:Superadmin|Adminfutsal|Adminkantin|Adminac|Adminservis'])
    ->group(function () {

    // Superadmin Routes
    Route::middleware(['role:Superadmin'])->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\SuperAdmin\DashboardController::class, 'index'])->name('dashboard');
        Route::resource('users', UserController::class);
        Route::get('/pelanggan', [UserController::class, 'indexPelanggan'])->name('users.pelanggan');
        Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
        Route::get('/transaksi/{id}', [TransaksiController::class, 'show'])->name('transaksi.show');
        Route::get('/monitoring', [AdminLogActivityController::class, 'index'])->name('dashboard.monitoring');
        Route::get('/rekap-keuangan', [RekapKeuanganController::class, 'index'])->name('dashboard.rekap-keuangan');
        Route::get('/pembagian-pendapatan', [PembagianPendapatanController::class, 'index'])->name('dashboard.pembagian-pendapatan');
        Route::put('/pembagian-pendapatan', [PembagianPendapatanController::class, 'updateKonfigurasi'])->name('dashboard.pembagian-pendapatan.update');
    });

    // Admin Futsal
    Route::middleware(['role:Adminfutsal'])->prefix('futsal')->name('futsal.')->group(function () {
        Route::get('/dashboard', [AdminFutsalDashboardController::class, 'index'])->name('dashboard');
        Route::resource('paket-membership', PaketMembershipController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::get('monitoring-paket', [MembershipController::class, 'index'])->name('monitoring-paket.index');
        Route::post('monitoring-membership', [MembershipController::class, 'store'])->name('monitoring-membership.store');
        Route::get('pelanggan', [PelangganController::class, 'index'])->name('pelanggan.index');
        Route::post('pelanggan', [PelangganController::class, 'store'])->name('pelanggan.store');
        Route::put('pelanggan/{user}', [PelangganController::class, 'update'])->name('pelanggan.update');
        Route::get('jadwal-lapangan', [JadwalLapanganController::class, 'index'])->name('jadwal-lapangan.index');
        Route::get('jadwal-lapangan/api-by-tanggal', [JadwalLapanganController::class, 'getJadwalByTanggal'])->name('jadwal-lapangan.api');
        Route::post('jadwal-lapangan/booking', [JadwalLapanganController::class, 'updateStatusBooking'])->name('jadwal-lapangan.booking');
        Route::post('jadwal-lapangan/batal', [JadwalLapanganController::class, 'batalBooking'])->name('jadwal-lapangan.batal');
        Route::get('booking/jadwal-tersedia', [\App\Http\Controllers\AdminFutsal\BookingController::class, 'getAvailableSlots'])->name('booking.jadwal_tersedia');
        Route::get('booking', [\App\Http\Controllers\AdminFutsal\BookingController::class, 'index'])->name('booking.index');
        Route::post('booking', [\App\Http\Controllers\AdminFutsal\BookingController::class, 'store'])->name('booking.store');
        Route::patch('booking/{id}', [\App\Http\Controllers\AdminFutsal\BookingController::class, 'update'])->name('booking.update');
        Route::patch('booking/{id}/cancel', [\App\Http\Controllers\AdminFutsal\BookingController::class, 'cancel'])->name('booking.cancel');
        Route::patch('booking/{id}/selesai', [\App\Http\Controllers\AdminFutsal\BookingController::class, 'selesai'])->name('booking.selesai');
        Route::get('transaksi', [AdminFutsalTransaksiController::class, 'index'])->name('transaksi.index');
        Route::get('transaksi/{id}', [AdminFutsalTransaksiController::class, 'show'])->name('transaksi.show');
        Route::patch('transaksi/{id}/konfirmasi', [AdminFutsalTransaksiController::class, 'konfirmasi'])->name('transaksi.konfirmasi');
        Route::patch('transaksi/{id}/reject', [AdminFutsalTransaksiController::class, 'reject'])->name('transaksi.reject');
        Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('laporan/export-pdf', [LaporanController::class, 'exportPdf'])->name('laporan.export.pdf');
        Route::get('laporan/export-excel', [LaporanController::class, 'exportExcel'])->name('laporan.export.excel');
        Route::get('pengaturan', [\App\Http\Controllers\AdminFutsal\PengaturanController::class, 'index'])->name('pengaturan.index');
        // Route jam-operasional dihapus, fitur sudah dipindah ke Pengaturan Dasar (jam blokir)
        // Route::get('pengaturan/jam-operasional', [\App\Http\Controllers\AdminFutsal\PengaturanController::class, 'indexJamOperasional'])->name('pengaturan.jam_operasional.index');
        // Route::post('pengaturan/jam-operasional', [\App\Http\Controllers\AdminFutsal\PengaturanController::class, 'storeJamOperasional'])->name('pengaturan.jam_operasional.store');
        Route::put('pengaturan/{id}', [\App\Http\Controllers\AdminFutsal\PengaturanController::class, 'update'])->name('pengaturan.update');
        Route::get('keuangan', [KeuanganController::class, 'index'])->name('keuangan.index');
        Route::post('keuangan/tambah-pengeluaran', [KeuanganController::class, 'storePengeluaran'])->name('keuangan.store');
        // Manajemen Lapangan
        Route::get('lapangan', [\App\Http\Controllers\AdminFutsal\LapanganController::class, 'index'])->name('lapangan.index');
        Route::get('lapangan/create', [\App\Http\Controllers\AdminFutsal\LapanganController::class, 'create'])->name('lapangan.create');
        Route::post('lapangan', [\App\Http\Controllers\AdminFutsal\LapanganController::class, 'store'])->name('lapangan.store');
        Route::get('lapangan/{id}/edit', [\App\Http\Controllers\AdminFutsal\LapanganController::class, 'edit'])->name('lapangan.edit');
        Route::put('lapangan/{id}', [\App\Http\Controllers\AdminFutsal\LapanganController::class, 'update'])->name('lapangan.update');
        Route::delete('lapangan/{id}', [\App\Http\Controllers\AdminFutsal\LapanganController::class, 'destroy'])->name('lapangan.destroy');
    });

    // Admin Kantin
    Route::middleware(['role:Adminkantin'])->prefix('kantin')->name('kantin.')->group(function () {
        Route::get('/dashboard', [AdminKantinDashboardController::class, 'index'])->name('dashboard');
        Route::post('kirim-wa/{id}', [AdminKantinDashboardController::class, 'kirimWaManual'])->name('kirim-wa');
        Route::resource('unit', AdminKantinUnitController::class);
        Route::patch('dokumentasi/{dokumen}/detail', [AdminKantinUnitController::class, 'updateDokumenDetail'])->name('unit.dokumen.updateDetail');
        Route::resource('penyewa', \App\Http\Controllers\AdminKantin\PenyewaController::class)->except(['create', 'store']);
        Route::resource('penyewaan', \App\Http\Controllers\AdminKantin\PenyewaanController::class);
        Route::get('penyewaan/{id}/generate-mou', [\App\Http\Controllers\AdminKantin\PenyewaanController::class, 'generateMOU'])->name('penyewaan.generate-mou');
        Route::post('penyewaan/{id}/upload-dokumen', [\App\Http\Controllers\AdminKantin\PenyewaanController::class, 'uploadDokumen'])->name('penyewaan.upload-dokumen');
        Route::delete('dokumen/{id}', [\App\Http\Controllers\AdminKantin\PenyewaanController::class, 'hapusDokumen'])->name('dokumen.hapus');
        Route::get('pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');
        Route::get('pembayaran/{pembayaran}', [PembayaranController::class, 'show'])->name('pembayaran.show');
        Route::put('pembayaran/{pembayaran}', [PembayaranController::class, 'update'])->name('pembayaran.update');
        Route::get('pembayaran/{pembayaran}/kwitansi', [PembayaranController::class, 'downloadKwitansi'])->name('pembayaran.kwitansi');
        Route::get('laporan', [\App\Http\Controllers\AdminKantin\LaporanController::class, 'index'])->name('laporan.index');
        Route::get('laporan/export-pdf', [\App\Http\Controllers\AdminKantin\LaporanController::class, 'exportPdf'])->name('laporan.export.pdf');
        Route::get('laporan/export-excel', [\App\Http\Controllers\AdminKantin\LaporanController::class, 'exportExcel'])->name('laporan.export.excel');
        Route::get('keuangan', [\App\Http\Controllers\AdminKantin\KeuanganController::class, 'index'])->name('keuangan.index');
        Route::post('keuangan', [\App\Http\Controllers\AdminKantin\KeuanganController::class, 'store'])->name('keuangan.store');

        // Audit Log
        Route::get('audit', [\App\Http\Controllers\AdminKantin\AuditLogController::class, 'index'])->name('audit.index');
        Route::get('audit/{id}', [\App\Http\Controllers\AdminKantin\AuditLogController::class, 'show'])->name('audit.show');
    });

    // Admin AC
    Route::middleware(['role:Adminac'])->prefix('ac')->name('ac.')->group(function () {
        Route::get('/dashboard', [AdminAcDashboardController::class, 'index'])->name('dashboard');
        Route::post('kategori', [AdminAcLayananController::class, 'storeKategori'])->name('kategori.store');
        Route::post('kategori-komponen', [\App\Http\Controllers\AdminAc\ProdukController::class, 'storeKategori'])->name('kategori_komponen.store');
        Route::resource('layanan', AdminAcLayananController::class);
        Route::resource('teknisi', AdminAcTeknisiController::class);
        Route::get('teknisi/{id}/cek-ketersediaan', [AdminAcTeknisiController::class, 'cekKetersediaan'])->name('teknisi.cek_ketersediaan');
        Route::resource('produk', \App\Http\Controllers\AdminAc\ProdukController::class)->except(['create', 'show', 'edit']);
        Route::patch('produk/{produk}/stok', [\App\Http\Controllers\AdminAc\ProdukController::class, 'updateStok'])->name('produk.updateStok');
        Route::get('pelanggan', [AdminAcPelangganController::class, 'index'])->name('pelanggan.index');
        Route::get('pelanggan/{id}', [AdminAcPelangganController::class, 'show'])->name('pelanggan.show');
        Route::get('booking', [AdminAcBookingController::class, 'index'])->name('booking.index');
        Route::post('booking/{id}/approve', [AdminAcBookingController::class, 'approve'])->name('booking.approve');
        Route::patch('booking/{id}/selesai', [AdminAcBookingController::class, 'selesai'])->name('booking.selesai');
        Route::get('booking-teknisi-tersedia', [AdminAcBookingController::class, 'teknisiTersedia'])->name('booking.teknisi_tersedia');
        Route::get('transaksi/histori', [AdminAcTransaksiController::class, 'history'])->name('transaksi.history');
        Route::resource('transaksi', AdminAcTransaksiController::class);
        Route::patch('transaksi/{id}/status', [AdminAcTransaksiController::class, 'updateStatus'])->name('transaksi.update_status');
        Route::get('laporan', [\App\Http\Controllers\AdminAc\LaporanController::class, 'index'])->name('laporan.index');
        Route::get('laporan/export-pdf', [\App\Http\Controllers\AdminAc\LaporanController::class, 'exportPdf'])->name('laporan.export.pdf');
        Route::get('laporan/export-excel', [\App\Http\Controllers\AdminAc\LaporanController::class, 'exportExcel'])->name('laporan.export.excel');
        Route::get('keuangan', [\App\Http\Controllers\AdminAc\KeuanganAcController::class, 'index'])->name('keuangan.index');
        Route::post('keuangan', [\App\Http\Controllers\AdminAc\KeuanganAcController::class, 'storePengeluaran'])->name('keuangan.store');
        Route::get('payroll', [\App\Http\Controllers\AdminAc\KeuanganAcController::class, 'payrollIndex'])->name('keuangan.payroll');
        Route::post('payroll', [\App\Http\Controllers\AdminAc\KeuanganAcController::class, 'storePayroll'])->name('keuangan.payroll.store');
    });

    // Admin Servis
    Route::middleware(['role:Adminservis'])->prefix('servis')->name('servis.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\AdminServis\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/profile', [\App\Http\Controllers\AdminServis\ProfileController::class, 'index'])->name('profile');
        Route::get('/transaksi/{kode}/invoice', [\App\Http\Controllers\AdminServis\DashboardController::class, 'invoice'])->name('transaksi.invoice');
        Route::get('/transaksi/{kode}/download-pdf', [\App\Http\Controllers\AdminServis\DashboardController::class, 'downloadPdf'])->name('transaksi.download-pdf');
        Route::get('/pelanggan', [\App\Http\Controllers\AdminServis\PelangganController::class, 'index'])->name('pelanggan.index');
        Route::get('/pelanggan/{id}', [\App\Http\Controllers\AdminServis\PelangganController::class, 'show'])->name('pelanggan.show');
        Route::prefix('transaksi')->name('transaksi.')->group(function () {
            Route::get('/', [\App\Http\Controllers\AdminServis\TransaksiController::class, 'index'])->name('index');
            Route::get('/{id}', [\App\Http\Controllers\AdminServis\TransaksiController::class, 'show'])->name('show');
        });
        Route::resource('layanan', \App\Http\Controllers\AdminServis\LayananController::class);
        Route::get('/booking', [\App\Http\Controllers\AdminServis\BookingController::class, 'index'])->name('booking.index');
        Route::get('/booking/{id}', [\App\Http\Controllers\AdminServis\BookingController::class, 'show'])->name('booking.show');
        Route::get('laporan', [\App\Http\Controllers\AdminServis\LaporanController::class, 'index'])->name('laporan.index');
        Route::get('laporan/export-pdf', [\App\Http\Controllers\AdminServis\LaporanController::class, 'exportPdf'])->name('laporan.export.pdf');
        Route::get('laporan/export-excel', [\App\Http\Controllers\AdminServis\LaporanController::class, 'exportExcel'])->name('laporan.export.excel');
        Route::resource('produk', \App\Http\Controllers\AdminServis\ProdukController::class)->except(['create', 'show', 'edit']);
        Route::get('/keuangan', [\App\Http\Controllers\AdminServis\KeuanganController::class, 'index'])->name('keuangan.index');
        Route::post('/keuangan/pengeluaran', [\App\Http\Controllers\AdminServis\KeuanganController::class, 'store'])->name('keuangan.store');
        Route::get('/keuangan/unpaid-pekerjaan/{teknisi_id}', [\App\Http\Controllers\AdminServis\KeuanganController::class, 'getUnpaidPekerjaan'])->name('keuangan.unpaid');
        Route::resource('teknisi', \App\Http\Controllers\AdminServis\TeknisiController::class);
        Route::patch('/teknisi/{id}/toggle-status', [\App\Http\Controllers\AdminServis\TeknisiController::class, 'toggleStatus'])->name('teknisi.toggle-status');

        Route::get('/audit', [\App\Http\Controllers\AdminServis\AuditLogController::class, 'index'])->name('audit.index');
        Route::get('/audit/{id}', [\App\Http\Controllers\AdminServis\AuditLogController::class, 'show'])->name('audit.show');
    });
});

// Profile Common (Semua Role yang Login)
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::put('/password', [\App\Http\Controllers\Auth\PasswordController::class, 'update'])->name('password.update');
});

// 2. TEKNISI (AC & Servis)
Route::prefix('teknisi')->name('teknisi.')->middleware(['auth', 'role:Teknisi|Teknisi Motor|Teknisi Mobil'])
    ->group(function () {
    
    // Teknisi AC
    Route::middleware(['role:Teknisi'])->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\TeknisiAc\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/pekerjaan/{id}', [\App\Http\Controllers\TeknisiAc\DashboardController::class, 'show'])->name('pekerjaan.show');
        Route::post('/pekerjaan/{id}/selesai', [\App\Http\Controllers\TeknisiAc\DashboardController::class, 'selesaikanPekerjaan'])->name('pekerjaan.selesai');
        Route::get('/pembayaran/{id}', [\App\Http\Controllers\TeknisiAc\PembayaranController::class, 'create'])->name('pembayaran.form');
        Route::post('/pembayaran/{id}', [\App\Http\Controllers\TeknisiAc\PembayaranController::class, 'store'])->name('pembayaran.store');
    });

    // Teknisi Servis
    Route::middleware(['role:Teknisi Motor|Teknisi Mobil'])->prefix('servis')->name('servis.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\TeknisiServis\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/pekerjaan/{id}', [\App\Http\Controllers\TeknisiServis\DashboardController::class, 'show'])->name('dashboard.show');
    });
});

// 3. KASIR (Servis)
Route::prefix('kasir')->name('kasir.')->middleware(['auth', 'role:Kasir'])
    ->group(function () {
    Route::get('/dashboard', [KasirDashboardController::class, 'index'])->name('dashboard');
    Route::get('/booking', [BookingKasirController::class, 'index'])->name('booking.index');
    Route::get('/booking/{id}', [BookingKasirController::class, 'show'])->name('booking.show');
    Route::put('/booking/{id}', [BookingKasirController::class, 'update'])->name('booking.update');
    Route::post('/booking/{id}/assign', [BookingKasirController::class, 'assignTeknisi'])->name('booking.assign');
    Route::get('/booking/{id}/print-wo', [BookingKasirController::class, 'printWo'])->name('booking.print-wo');
    Route::post('/booking/{id}/rincian', [BookingKasirController::class, 'simpanRincian'])->name('booking.simpan-rincian');
    Route::post('/booking/{id}/lanjut-pembayaran', [BookingKasirController::class, 'lanjutPembayaran'])->name('booking.lanjut-pembayaran');
    Route::get('/pembayaran', [BookingKasirController::class, 'indexPembayaran'])->name('pembayaran.index');
    Route::get('/pembayaran/{id}', [BookingKasirController::class, 'showPembayaran'])->name('pembayaran.show');
    Route::post('/pembayaran/{id}/konfirmasi', [BookingKasirController::class, 'konfirmasiPembayaran'])->name('pembayaran.konfirmasi');

    // Laporan
    Route::get('/laporan', [\App\Http\Controllers\KasirServis\LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/export-pdf', [\App\Http\Controllers\KasirServis\LaporanController::class, 'exportPdf'])->name('laporan.export.pdf');
    Route::get('/laporan/export-excel', [\App\Http\Controllers\KasirServis\LaporanController::class, 'exportExcel'])->name('laporan.export.excel');
});

// 4. KASIR FUTSAL
use App\Http\Controllers\KasirFutsal;

Route::middleware(['auth', 'checkRole:kasirfutsal'])->prefix('kasir-futsal')->name('kasirfutsal.')->group(function () {
    Route::get('/', [KasirFutsal\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/booking', [KasirFutsal\BookingKasirFutsalController::class, 'index'])->name('booking.index');
    Route::get('/booking/{id}', [KasirFutsal\BookingKasirFutsalController::class, 'show'])->name('booking.show');
    Route::post('/booking/{id}/konfirmasi', [KasirFutsal\BookingKasirFutsalController::class, 'konfirmasi'])->name('booking.konfirmasi');

    Route::get('/pembayaran', [KasirFutsal\PembayaranKasirFutsalController::class, 'index'])->name('pembayaran.index');
    Route::get('/pembayaran/{id}', [KasirFutsal\PembayaranKasirFutsalController::class, 'show'])->name('pembayaran.show');
    Route::post('/pembayaran/{id}/proses', [KasirFutsal\PembayaranKasirFutsalController::class, 'prosesPembayaran'])->name('pembayaran.proses');
    Route::post('/pembayaran/{id}/tolak', [KasirFutsal\PembayaranKasirFutsalController::class, 'tolakPembayaran'])->name('pembayaran.tolak');

    Route::get('/laporan', [KasirFutsal\LaporanKasirFutsalController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/pdf', [KasirFutsal\LaporanKasirFutsalController::class, 'exportPdf'])->name('laporan.pdf');
});