<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Transaksi Futsal</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .header { margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.table-bordered th, table.table-bordered td { border: 1px solid #ddd; padding: 8px; }
        table.table-bordered th { background-color: #f2f2f2; }
        .total-box { padding: 10px; background-color: #eafaf1; border: 1px solid #b2e6c5; width: 300px; float: right; margin-top: 10px;}
        .clearfix::after { content: ""; clear: both; display: table; }
    </style>
</head>
<body>

    <div class="header text-center">
        <h2 style="margin:0;">LAPORAN TRANSAKSI BOOKING FUTSAL</h2>
        <p style="margin-5px 0 0 0;">
            Filter: 
            @if(request('tanggal_dari') && request('tanggal_sampai'))
                Periode {{ request('tanggal_dari') }} s/d {{ request('tanggal_sampai') }}
            @elseif(request('tanggal_dari'))
                Sejak {{ request('tanggal_dari') }}
            @elseif(request('tanggal_sampai'))
                Hingga {{ request('tanggal_sampai') }}
            @else
                Semua Waktu
            @endif
        </p>
    </div>

    <table class="table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Pelanggan</th>
                <th>Tgl Booking</th>
                <th>Tgl Pembayaran</th>
                <th>Jenis Pembayaran</th>
                <th class="text-right">Jumlah Bayar</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($laporan as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->booking->user->name ?? '-' }}</td>
                    <td>{{ $item->booking->bookingFutsal->tgl_main ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tgl_bayar)->format('d-m-Y H:i') }}</td>
                    <td>{{ $item->booking->bookingFutsal->jenis_pembayaran ?? ($item->tipePembayaran->nama_tipe ?? '-') }}</td>
                    <td class="text-right">Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $item->status }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Data tidak ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="clearfix">
        <div class="total-box">
            <span class="font-bold">Total Pendapatan (Status Verifikasi):</span><br>
            <span style="font-size: 16px; color: #1e8449;" class="font-bold">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</span>
        </div>
    </div>

</body>
</html>
