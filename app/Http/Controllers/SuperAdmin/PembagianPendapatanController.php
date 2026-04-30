<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PembagianPendapatanController extends Controller
{
    // Penerima yang valid per sistem
    private array $penerimaBySistem = [
        'ac'     => ['jurusan', 'aplikasi', 'blud'],
        'kantin' => ['aplikasi', 'blud'],
        'futsal' => ['aplikasi', 'blud'],
        'servis' => ['jurusan', 'aplikasi', 'blud'],
    ];

    // Persentase default yang dipakai jika belum pernah dikonfigurasi
    private array $defaultPersentase = [
        'ac'     => ['jurusan' => 40.00, 'aplikasi' => 10.00, 'blud' => 50.00],
        'kantin' => ['aplikasi' => 20.00, 'blud' => 80.00],
        'futsal' => ['aplikasi' => 20.00, 'blud' => 80.00],
        'servis' => ['jurusan' => 40.00, 'aplikasi' => 10.00, 'blud' => 50.00],
    ];

    public function index(Request $request)
    {
        $startDate = $request->start_date;
        $endDate   = $request->end_date;

        // ── Auto-init konfigurasi default jika tabel belum terisi ─
        $this->autoInitKonfigurasi();

        // ── Ambil konfigurasi persentase ──────────────────────────
        $konfigurasi = DB::table('pembagian_pendapatan')
            ->get()
            ->groupBy('sistem')
            ->map(fn($rows) => $rows->keyBy('penerima'));

        // ── Hitung saldo bersih per sistem ────────────────────────
        $saldoAc     = $this->getSaldoAc($startDate, $endDate);
        $saldoKantin = $this->getSaldoKantin($startDate, $endDate);
        $saldoFutsal = $this->getSaldoFutsal($startDate, $endDate);
        $saldoServis = $this->getSaldoServis($startDate, $endDate);

        // ── Hitung pembagian ──────────────────────────────────────
        $pembagian = [
            'ac'     => $this->hitungPembagian('ac',     $saldoAc,     $konfigurasi),
            'kantin' => $this->hitungPembagian('kantin', $saldoKantin, $konfigurasi),
            'futsal' => $this->hitungPembagian('futsal', $saldoFutsal, $konfigurasi),
            'servis' => $this->hitungPembagian('servis', $saldoServis, $konfigurasi),
        ];

        // ── Rekapitulasi global per penerima ──────────────────────
        $rekapPenerima = $this->hitungRekapPenerima($pembagian);

        return view('dashboard.pembagian-pendapatan', compact(
            'konfigurasi',
            'pembagian',
            'rekapPenerima',
            'saldoAc',
            'saldoKantin',
            'saldoFutsal',
            'saldoServis',
            'startDate',
            'endDate',
        ));
    }

    public function updateKonfigurasi(Request $request)
    {
        $request->validate([
            'sistem'       => 'required|in:ac,kantin,futsal,servis',
            'persentase'   => 'required|array',
            'persentase.*' => 'required|numeric|min:0|max:100',
        ]);

        $sistem = $request->sistem;
        $valid  = $this->penerimaBySistem[$sistem];
        $total  = collect($request->persentase)->only($valid)->sum();

        if (round($total, 2) != 100.00) {
            return back()->withErrors([
                'persentase' => "Total persentase sistem {$sistem} harus 100%. Saat ini: {$total}%"
            ])->withInput();
        }

        foreach ($request->persentase as $penerima => $persen) {
            if (!in_array($penerima, $valid)) continue;

            DB::table('pembagian_pendapatan')
                ->updateOrInsert(
                    ['sistem' => $sistem, 'penerima' => $penerima],
                    ['persentase' => $persen, 'updated_at' => now()]
                );
        }

        return back()->with('success', 'Konfigurasi pembagian sistem ' . strtoupper($sistem) . ' berhasil diperbarui.');
    }

    // ── Private helpers ───────────────────────────────────────────

    private function getSaldoAc(?string $start, ?string $end): float
    {
        $masuk = (float) DB::table('pembayaran_ac')->where('status', 'dibayar')
            ->when($start, fn($q) => $q->whereDate('tgl_bayar', '>=', $start))
            ->when($end,   fn($q) => $q->whereDate('tgl_bayar', '<=', $end))
            ->sum('total_harga');

        $keluar = (float) DB::table('pengeluaran_ac')
            ->when($start, fn($q) => $q->whereDate('tanggal', '>=', $start))
            ->when($end,   fn($q) => $q->whereDate('tanggal', '<=', $end))
            ->sum('nominal');

        return $masuk - $keluar;
    }

    private function getSaldoKantin(?string $start, ?string $end): float
    {
        $masuk = (float) DB::table('pembayaran_ruko')->where('status', 'verifikasi')
            ->when($start, fn($q) => $q->whereDate('tgl_bayar', '>=', $start))
            ->when($end,   fn($q) => $q->whereDate('tgl_bayar', '<=', $end))
            ->sum('jumlah_tagihan');

        $keluar = (float) DB::table('pengeluaran_kantin')
            ->when($start, fn($q) => $q->whereDate('tanggal', '>=', $start))
            ->when($end,   fn($q) => $q->whereDate('tanggal', '<=', $end))
            ->sum('nominal');

        return $masuk - $keluar;
    }

    private function getSaldoFutsal(?string $start, ?string $end): float
    {
        $masuk = (float) DB::table('pembayaran_futsal')->where('status', 'verifikasi')
            ->when($start, fn($q) => $q->whereDate('tgl_bayar', '>=', $start))
            ->when($end,   fn($q) => $q->whereDate('tgl_bayar', '<=', $end))
            ->sum('jumlah_bayar');

        $keluar = (float) DB::table('pengeluaran_futsals')
            ->when($start, fn($q) => $q->whereDate('tgl_pengeluaran', '>=', $start))
            ->when($end,   fn($q) => $q->whereDate('tgl_pengeluaran', '<=', $end))
            ->sum('nominal');

        return $masuk - $keluar;
    }

    private function getSaldoServis(?string $start, ?string $end): float
    {
        $masuk = (float) DB::table('pembayaran_servis')->where('status_pembayaran', 'lunas')
            ->when($start, fn($q) => $q->whereDate('tanggal_bayar', '>=', $start))
            ->when($end,   fn($q) => $q->whereDate('tanggal_bayar', '<=', $end))
            ->sum('total_biaya');

        $keluar = (float) DB::table('pengeluaran_servis')
            ->when($start, fn($q) => $q->whereDate('tanggal', '>=', $start))
            ->when($end,   fn($q) => $q->whereDate('tanggal', '<=', $end))
            ->sum('jumlah');

        return $masuk - $keluar;
    }

    private function hitungPembagian(string $sistem, float $saldo, $konfigurasi): array
    {
        $result = ['saldo_bersih' => $saldo, 'detail' => []];
        $rows   = $konfigurasi->get($sistem, collect());

        foreach ($this->penerimaBySistem[$sistem] as $penerima) {
            $persen  = (float) ($rows->get($penerima)?->persentase ?? 0);
            $nominal = $saldo * ($persen / 100);

            $result['detail'][$penerima] = [
                'persentase' => $persen,
                'nominal'    => $nominal,
            ];
        }

        return $result;
    }

    private function hitungRekapPenerima(array $pembagian): array
    {
        $rekap = [];

        foreach ($pembagian as $sistem => $data) {
            foreach ($data['detail'] as $penerima => $info) {
                $rekap[$penerima] = ($rekap[$penerima] ?? 0) + $info['nominal'];
            }
        }

        arsort($rekap);
        return $rekap;
    }

    /**
     * Pastikan tabel pembagian_pendapatan sudah terisi konfigurasi default
     * untuk semua sistem. Jika ada sistem yang belum punya baris, insert default.
     */
    private function autoInitKonfigurasi(): void
    {
        foreach ($this->defaultPersentase as $sistem => $penerimas) {
            foreach ($penerimas as $penerima => $persen) {
                DB::table('pembagian_pendapatan')->updateOrInsert(
                    ['sistem' => $sistem, 'penerima' => $penerima],
                    [
                        'persentase' => $persen,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}
