<?php

namespace App\Exports;

use App\Models\PembayaranServis;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanServisExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = PembayaranServis::with([
            'bookingServis.pelanggan',
            'bookingServis.layananServis',
            'bookingServis.teknisi',
            'bookingServis.rincianServis',
        ]);

        if ($this->request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_bayar', '>=', $this->request->tanggal_dari);
        }

        if ($this->request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_bayar', '<=', $this->request->tanggal_sampai);
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
            'Nama Pelanggan',
            'Layanan',
            'Teknisi',
            'Tipe Kendaraan',
            'Merek Kendaraan',
            'No Plat',
            'Tgl Booking',
            'Tgl Bayar',
            'Total Biaya',
            'Tipe Pembayaran',
            'Status Pembayaran',
        ];
    }

    protected $rowNumber = 0;

    public function map($item): array
    {
        $this->rowNumber++;

        $tglBooking = '-';
        if (!empty($item->bookingServis->tanggal_booking)) {
            try {
                $tglBooking = Carbon::parse($item->bookingServis->tanggal_booking)->format('d-m-Y');
            } catch (\Exception $e) {
                $tglBooking = '-';
            }
        }

        $tglBayar = '-';
        if (!empty($item->tanggal_bayar)) {
            try {
                $tglBayar = Carbon::parse($item->tanggal_bayar)->format('d-m-Y H:i');
            } catch (\Exception $e) {
                $tglBayar = '-';
            }
        }

        return [
            $this->rowNumber,
            $item->bookingServis->kode_booking ?? '-',
            $item->bookingServis->pelanggan->name ?? $item->bookingServis->pelanggan->nama_lengkap ?? '-',
            $item->bookingServis->layananServis->nama_layanan ?? '-',
            $item->bookingServis->teknisi->name ?? '-',
            $item->bookingServis->tipe_kendaraan ?? '-',
            $item->bookingServis->merek_kendaraan ?? '-',
            $item->bookingServis->nomor_plat ?? '-',
            $tglBooking,
            $tglBayar,
            'Rp ' . number_format($item->total_biaya, 0, ',', '.'),
            $item->tipe_pembayaran ?? '-',
            $item->status_pembayaran ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
