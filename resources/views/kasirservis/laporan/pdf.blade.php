<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kasir Servis Kendaraan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 0;
            padding: 0;
            font-size: 18px;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 12px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th {
            background-color: #f4f4f4;
            padding: 8px;
            text-align: left;
            font-weight: bold;
        }
        td {
            padding: 8px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .summary-table {
            width: 50%;
            margin-left: auto;
            border: none;
        }
        .summary-table th, .summary-table td {
            border: none;
            padding: 5px;
        }
        .summary-table th {
            text-align: left;
            background-color: transparent;
        }
        .summary-table .total-row {
            font-size: 14px;
            font-weight: bold;
            border-top: 1px solid #333;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 12px;
        }
        .signature {
            margin-top: 50px;
        }
        .signature p {
            margin: 0;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>Laporan Transaksi Kasir Servis Kendaraan</h2>
        <p>BLUD System - Layanan Servis Kendaraan</p>
        <p>
            Filter: 
            @if(request('tanggal_dari') && request('tanggal_sampai'))
                Tanggal {{ request('tanggal_dari') }} s/d {{ request('tanggal_sampai') }}
            @elseif(request('tanggal_dari'))
                Sejak Tanggal {{ request('tanggal_dari') }}
            @elseif(request('tanggal_sampai'))
                Hingga Tanggal {{ request('tanggal_sampai') }}
            @else
                Semua Tanggal
            @endif
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="30" class="text-center">No</th>
                <th width="100">Tanggal</th>
                <th width="100">Kode Booking</th>
                <th>Nama Pelanggan</th>
                <th>Layanan Servis</th>
                <th width="80" class="text-center">Metode</th>
                <th width="80" class="text-center">Status</th>
                <th width="100" class="text-right">Total Biaya</th>
            </tr>
        </thead>
        <tbody>
            @forelse($laporan as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->tanggal_bayar ? $item->tanggal_bayar->format('d/m/Y H:i') : '-' }}</td>
                    <td>{{ $item->bookingServis->kode_booking ?? '-' }}</td>
                    <td>{{ $item->bookingServis->pelanggan->name ?? '-' }}</td>
                    <td>{{ $item->bookingServis->layananServis->nama_layanan ?? '-' }}</td>
                    <td class="text-center text-uppercase">{{ $item->tipe_pembayaran ?? '-' }}</td>
                    <td class="text-center text-uppercase">{{ str_replace('_', ' ', $item->status_pembayaran) }}</td>
                    <td class="text-right">Rp {{ number_format($item->total_biaya, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data transaksi yang ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="summary-table">
        <tr>
            <th>Total Transaksi:</th>
            <td class="text-right">{{ $laporan->count() }}</td>
        </tr>
        <tr>
            <th>Transaksi Lunas:</th>
            <td class="text-right">{{ $laporan->where('status_pembayaran', 'lunas')->count() }}</td>
        </tr>
        <tr class="total-row">
            <th>Total Pendapatan Lunas:</th>
            <td class="text-right">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ date('d/m/Y H:i:s') }}</p>
        <div class="signature">
            <p>Kasir Servis,</p>
            <br><br><br>
            <p>( ................................. )</p>
        </div>
    </div>

</body>
</html>
