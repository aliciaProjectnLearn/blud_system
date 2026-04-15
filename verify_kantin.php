<?php
// Script verifikasi konsistensi data kantin
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "\n===== VERIFIKASI DATA KANTIN =====\n\n";

// 1. Cek semua unit dan harga
echo "--- UNIT RUKO (semua) ---\n";
$units = DB::table('ruko')
    ->join('kategori', 'ruko.kategori_id', '=', 'kategori.id')
    ->select('ruko.kode_unit', 'kategori.nama as kategori', 'ruko.harga', 'ruko.status_unit')
    ->orderBy('ruko.kode_unit')
    ->get();
foreach ($units as $u) {
    echo "  {$u->kode_unit} | {$u->kategori} | Rp " . number_format($u->harga, 0, ',', '.') . " | {$u->status_unit}\n";
}

// 2. Cek sewa + pembayaran konsistensi
echo "\n--- SEWA + PEMBAYARAN ---\n";
$sewas = DB::table('sewa_ruko')
    ->join('ruko', 'sewa_ruko.ruko_id', '=', 'ruko.id')
    ->join('penyewa', 'sewa_ruko.penyewa_id', '=', 'penyewa.id')
    ->select('ruko.kode_unit', 'sewa_ruko.id as sewa_id', 'sewa_ruko.status', 
             'sewa_ruko.tgl_mulai', 'sewa_ruko.tgl_selesai', 'ruko.harga', 'penyewa.nama_usaha')
    ->orderBy('ruko.kode_unit')
    ->get();

foreach ($sewas as $s) {
    $pembayaran = DB::table('pembayaran_ruko')
        ->where('sewa_ruko_id', $s->sewa_id)
        ->orderBy('termin')
        ->get();

    echo "\n  [{$s->kode_unit}] Sewa #{$s->sewa_id} | Status: {$s->status} | {$s->nama_usaha}\n";
    echo "    Mulai: {$s->tgl_mulai} → Selesai: {$s->tgl_selesai} | Harga: Rp " . number_format($s->harga, 0, ',', '.') . "\n";
    foreach ($pembayaran as $p) {
        $kwt = $p->no_kwitansi ?? '-';
        echo "    T{$p->termin}: Rp " . number_format($p->jumlah_tagihan, 0, ',', '.') 
             . " | JT: {$p->tgl_jatuh_tempo} | Status: {$p->status} | KWT: {$kwt}\n";
    }

    // Validasi
    if (count($pembayaran) !== 2) {
        echo "    [ERROR] Jumlah pembayaran harus 2, ditemukan: " . count($pembayaran) . "\n";
    }
    $totalPembayaran = $pembayaran->sum('jumlah_tagihan');
    if ($totalPembayaran != $s->harga) {
        echo "    [ERROR] Total pembayaran Rp " . number_format($totalPembayaran, 0, ',', '.') . " != harga Rp " . number_format($s->harga, 0, ',', '.') . "\n";
    }
}

// 3. Penyewa
echo "\n--- PENYEWA ---\n";
$penyewas = DB::table('penyewa')
    ->join('users', 'penyewa.user_id', '=', 'users.id')
    ->select('penyewa.id', 'users.username', 'penyewa.nama_usaha', 'penyewa.alamat')
    ->get();
foreach ($penyewas as $p) {
    echo "  #{$p->id} [{$p->username}] {$p->nama_usaha} | {$p->alamat}\n";
}

echo "\n===== SELESAI =====\n";
