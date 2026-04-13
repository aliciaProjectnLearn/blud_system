<?php

namespace App\Exports;

use App\Models\PembayaranFutsal;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanFutsalExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = PembayaranFutsal::with([
            'booking.user',
            'booking.bookingFutsal',
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
            'ID Transaksi',
            'Nama Pelanggan',
            'Tanggal Booking',
            'Tanggal Pembayaran',
            'Jumlah Bayar',
            'Jenis Pembayaran',
            'Status Transaksi',
        ];
    }

    public function map($pembayaran): array
    {
        $namaPelanggan = $pembayaran->booking->user->name ?? '-';
        $bookingFutsal = $pembayaran->booking->bookingFutsal;
        if ($bookingFutsal && $bookingFutsal->type === 'event') {
            $tanggalBooking = \Carbon\Carbon::parse($bookingFutsal->start_datetime)->format('d-m-Y') . ' s/d ' . \Carbon\Carbon::parse($bookingFutsal->end_datetime)->format('d-m-Y');
        } elseif ($bookingFutsal) {
            $tanggalBooking = \Carbon\Carbon::parse($bookingFutsal->start_datetime)->format('d-m-Y H:i');
        } else {
            $tanggalBooking = '-';
        }
        $tanggalPembayaran = \Carbon\Carbon::parse($pembayaran->tgl_bayar)->format('d-m-Y');
        $jumlahBayar = $pembayaran->jumlah_bayar;
        $jenisPembayaran = $pembayaran->booking->bookingFutsal->jenis_pembayaran ?? 
                           $pembayaran->tipePembayaran->nama_tipe ?? '-';
        $statusTransaksi = $pembayaran->status;

        return [
            $pembayaran->kode_pembayaran,
            $namaPelanggan,
            $tanggalBooking,
            $tanggalPembayaran,
            $jumlahBayar,
            $jenisPembayaran,
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
