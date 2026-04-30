<?php

namespace App\Exports;

use App\Models\PembayaranServis;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanKasirServisExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = PembayaranServis::with([
            'bookingServis.pelanggan',
            'bookingServis.layananServis',
        ]);

        if ($this->request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_bayar', '>=', $this->request->tanggal_dari);
        }

        if ($this->request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_bayar', '<=', $this->request->tanggal_sampai);
        }

        if ($this->request->filled('metode_pembayaran')) {
            $query->where('tipe_pembayaran', $this->request->metode_pembayaran);
        }

        if ($this->request->filled('status')) {
            $query->where('status_pembayaran', $this->request->status);
        }

        return $query->orderBy('tanggal_bayar', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Booking',
            'Tanggal Transaksi',
            'Nama Pelanggan',
            'Layanan Servis',
            'Metode Pembayaran',
            'Status',
            'Total Biaya (Rp)'
        ];
    }

    public function map($pembayaran): array
    {
        static $rowNumber = 0;
        $rowNumber++;

        return [
            $rowNumber,
            $pembayaran->bookingServis->kode_booking ?? '-',
            $pembayaran->tanggal_bayar ? $pembayaran->tanggal_bayar->format('d/m/Y H:i') : '-',
            $pembayaran->bookingServis->pelanggan->name ?? '-',
            $pembayaran->bookingServis->layananServis->nama_layanan ?? '-',
            ucfirst($pembayaran->tipe_pembayaran ?? '-'),
            ucfirst(str_replace('_', ' ', $pembayaran->status_pembayaran)),
            $pembayaran->total_biaya
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
