<?php

namespace App\Exports;

use App\Models\PembayaranAc;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanAcExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = PembayaranAc::with([
            'bookingAc.user',
            'bookingAc.layanan',
            'bookingAc.teknisi',
            'tipePembayaran'
        ]);

        if ($this->request->filled('tanggal_dari') && $this->request->filled('tanggal_sampai')) {
            $query->whereBetween('tgl_bayar', [$this->request->tanggal_dari . ' 00:00:00', $this->request->tanggal_sampai . ' 23:59:59']);
        } elseif ($this->request->filled('tanggal_dari')) {
            $query->where('tgl_bayar', '>=', $this->request->tanggal_dari . ' 00:00:00');
        } elseif ($this->request->filled('tanggal_sampai')) {
            $query->where('tgl_bayar', '<=', $this->request->tanggal_sampai . ' 23:59:59');
        }

        return $query->orderBy('tgl_bayar', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'No Invoice',
            'Nama Pelanggan',
            'Layanan',
            'Teknisi',
            'Tgl Kunjungan',
            'Tgl Pembayaran',
            'Total Harga',
            'Tipe Pembayaran',
            'Status',
        ];
    }

    public function map($pembayaran): array
    {
        $noInvoice = $pembayaran->invoice_no ?? '-';
        $namaPelanggan = $pembayaran->bookingAc->user->name ?? '-';
        $layanan = $pembayaran->bookingAc->layanan->nama_layanan ?? ($pembayaran->bookingAc->layanan->nama ?? '-');
        $teknisi = $pembayaran->bookingAc->teknisi->name ?? '-';
        $tglKunjungan = $pembayaran->bookingAc->tgl_kunjungan ?? '-';
        $tglPembayaran = \Carbon\Carbon::parse($pembayaran->tgl_bayar)->format('d-m-Y');
        $totalHarga = 'Rp ' . number_format($pembayaran->total_harga, 0, ',', '.');
        $tipePembayaran = $pembayaran->tipePembayaran->nama_tipe ?? '-';
        $statusTransaksi = $pembayaran->status;

        return [
            $noInvoice,
            $namaPelanggan,
            $layanan,
            $teknisi,
            $tglKunjungan,
            $tglPembayaran,
            $totalHarga,
            $tipePembayaran,
            $statusTransaksi,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
