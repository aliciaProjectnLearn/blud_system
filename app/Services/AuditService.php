<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditService
{
    /**
     * Catat audit log
     *
     * @param string $sistem        Nama sistem (kantin, futsal, ac, servis, global)
     * @param string $tabelEntitas  Nama tabel (sewa_ruko, pembayaran_ruko, dll)
     * @param int|null $entitasId   ID data yang diubah
     * @param string $aksi          Jenis aksi
     * @param array|null $dataLama  Data sebelum perubahan
     * @param array|null $dataBaru  Data setelah perubahan
     * @param string|null $keterangan Catatan tambahan
     */
    public static function catat(
        string $sistem,
        string $tabelEntitas,
        ?int $entitasId,
        string $aksi,
        ?array $dataLama = null,
        ?array $dataBaru = null,
        ?string $keterangan = null
    ): void {
        try {
            $admin = Auth::user();

            // Hanya simpan field yang benar-benar berubah
            $perubahanBersih = null;
            if ($dataLama && $dataBaru) {
                $perubahanBersih = [];
                foreach ($dataBaru as $key => $nilaBaru) {
                    $nilaLama = $dataLama[$key] ?? null;
                    if ($nilaLama !== $nilaBaru) {
                        $perubahanBersih[$key] = [
                            'dari' => $nilaLama,
                            'ke'   => $nilaBaru,
                        ];
                    }
                }
                // Jika tidak ada yang berubah, skip
                if (empty($perubahanBersih) && $aksi === 'data_diupdate') {
                    return;
                }
            }

            AuditLog::create([
                'sistem'         => $sistem,
                'tabel_entitas'  => $tabelEntitas,
                'entitas_id'     => $entitasId,
                'aksi'           => $aksi,
                'data_lama'      => $dataLama 
                                    ? self::masking($dataLama) 
                                    : null,
                'data_baru'      => $perubahanBersih ?? ($dataBaru 
                                    ? self::masking($dataBaru) 
                                    : null),
                'tipe_pelaku'    => $admin ? 'admin' : 'sistem',
                'dilakukan_oleh' => $admin?->id,
                'nama_pelaku'    => $admin?->name ?? 'Sistem',
                'keterangan'     => $keterangan,
                'ip_address'     => Request::ip(),
            ]);
        } catch (\Exception $e) {
            // Fail-safe: jika audit gagal, jangan ganggu proses utama
            \Illuminate\Support\Facades\Log::error('Audit log gagal: ' . $e->getMessage());
        }
    }

    /**
     * Masking data sensitif
     */
    private static function masking(array $data): array
    {
        $sensitif = ['nik_penyewa', 'nik', 'password', 'access_token'];
        foreach ($sensitif as $field) {
            if (isset($data[$field])) {
                $val = (string) $data[$field];
                $data[$field] = substr($val, 0, 4) . 
                                str_repeat('*', max(0, strlen($val) - 8)) . 
                                substr($val, -4);
            }
        }
        return $data;
    }
}
