<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminLogActivityController; // ← nama controller yang benar
use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\SuperAdmin\TransaksiController;

Route::middleware(['auth', 'role:Superadmin'])->group(function () {
    Route::get('/superadmin/monitoring', [AdminLogActivityController::class, 'index'])->name('monitoring.index');
});

// route lain...
// ── Halaman Welcome ──────────────────────────────────────
Route::get('/', function () {
    return redirect()->route('login');
});

// ── Auth Routes (Login & Register) ───────────────────────
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// ── Protected Routes (perlu login) ───────────────────────
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\SuperAdmin\DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // User Management
    Route::resource('users', UserController::class);
    Route::get('/pelanggan', [UserController::class, 'indexPelanggan'])->name('users.pelanggan');

    Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');

});