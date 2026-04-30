<?php

namespace App\Exports;

use App\Models\PembayaranRuko;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanKantinExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = PembayaranRuko::with([
            'sewaRuko.penyewa.user',
            'sewaRuko.ruko',
            'tipe'
        ])->whereHas('sewaRuko', function ($q) {
            $q->whereNotIn('status_sewa', ['dibatalkan', 'ditolak']);
        });

        if ($this->request->filled('tanggal_dari') && $this->request->filled('tanggal_sampai')) {
            $query->whereBetween('tanggal_bayar', [$this->request->tanggal_dari . ' 00:00:00', $this->request->tanggal_sampai . ' 23:59:59']);
        } elseif ($this->request->filled('tanggal_dari')) {
            $query->where('tanggal_bayar', '>=', $this->request->tanggal_dari . ' 00:00:00');
        } elseif ($this->request->filled('tanggal_sampai')) {
            $query->where('tanggal_bayar', '<=', $this->request->tanggal_sampai . ' 23:59:59');
        }

        return $query->orderBy('tanggal_bayar', 'desc')->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'ID Pembayaran (Kwitansi)',
            'Nama Penyewa',
            'Kode Ruko',
            'Tanggal Sewa',
            'Tanggal Bayar',
            'Jumlah Bayar',
            'Status',
        ];
    }

    public function map($pembayaran): array
    {
        $namaPenyewa = $pembayaran->sewaRuko?->penyewa?->nama_usaha ?? '-';
        $kodeRuko    = $pembayaran->sewaRuko?->ruko?->kode_unit ?? '-';
        $tglSewa     = '-';
        
        if ($pembayaran->sewaRuko) {
            $tglSewa = \Carbon\Carbon::parse($pembayaran->sewaRuko->tgl_mulai)->format('d/m/Y') . ' - ' . \Carbon\Carbon::parse($pembayaran->sewaRuko->tgl_selesai)->format('d/m/Y');
        }
        
        $tglBayar    = $pembayaran->tanggal_bayar ? \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->format('d/m/Y') : '-';
        $jumlahBayar = $pembayaran->jumlah_tagihan;
        $status      = ucfirst($pembayaran->status);

        return [
            $pembayaran->no_kwitansi ?? '-',
            $namaPenyewa,
            $kodeRuko,
            $tglSewa,
            $tglBayar,
            $jumlahBayar,
            $status,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
